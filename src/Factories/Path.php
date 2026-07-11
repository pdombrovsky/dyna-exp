<?php

namespace DynaExp\Factories;

use DynaExp\Builders\ProjectableInterface;
use DynaExp\Enums\AttributeTypeEnum;
use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Factories\IfNotExists;
use DynaExp\Factories\Size;
use DynaExp\Factories\Traits\ConditionTrait;
use DynaExp\Factories\Traits\OperationTrait;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\PathNode;
use Stringable;

final readonly class Path implements Stringable, ProjectableInterface, ExpressionOperandInterface
{
    use ConditionTrait;
    use OperationTrait;

    /**
     * @param PathNode $pathNode
     */
    private function __construct(private PathNode $pathNode)
    {
    }

    /**
     * @return PathNode
     */
    public function project(): PathNode
    {
        return $this->pathNode;
    }

    /**
     * Creates a condition to check if the attribute exists.
     *
     * @return Condition
     */
    public function attributeExists(): Condition
    {
        return Condition::attributeExists($this->pathNode);
    }

    /**
     * Creates a condition to check if the attribute does not exist.
     *
     * @return Condition
     */
    public function attributeNotExists(): Condition
    {
        return Condition::attributeNotExists($this->pathNode);
    }

    /**
     * Creates a condition to check the type of the attribute.
     *
     * @param AttributeTypeEnum $type The expected attribute type.
     * @return Condition
     */
    public function attributeType(AttributeTypeEnum $type): Condition
    {
        return Condition::attributeType($this->pathNode, $type->value);
    }

    /**
     * Creates a condition to ensure the attribute type is not the specified type.
     *
     * @param AttributeTypeEnum $type The expected attribute type.
     * @return Condition
     */
    public function attributeTypeNot(AttributeTypeEnum $type): Condition
    {
        return Condition::not($this->attributeType($type));
    }

    /**
     * Creates a condition to check if the attribute begins with a specified prefix.
     *
     * @param mixed $prefix The prefix to check.
     * @return Condition
     */
    public function beginsWith(mixed $prefix): Condition
    {
        return Condition::beginsWith($this->pathNode, $prefix);
    }

    /**
     * Creates a condition to check if the attribute not begins with a specified prefix.
     *
     * @param mixed $prefix The prefix to check.
     * @return Condition
     */
    public function notBeginsWith(mixed $prefix): Condition
    {
        return Condition::not($this->beginsWith($prefix));
    }

    /**
     * Creates a condition to check if the attribute contains a specified value.
     *
     * @param mixed $value The value to check for containment.
     * @return Condition
     */
    public function contains(mixed $value): Condition
    {
        return Condition::contains($this->pathNode, $value);
    }

    /**
     * Creates a condition to check if the attribute not contains a specified value.
     *
     * @param mixed $value The value to check for containment.
     * @return Condition
     */
    public function notContains(mixed $value): Condition
    {
        return Condition::not($this->contains($value));
    }

    /**
     * Retrieves the size of the attribute.
     *
     * @return Size
     */
    public function size(): Size
    {
        return new Size($this->pathNode);
    }

    /**
     * Sets a value if the attribute does not exist.
     *
     * @param mixed $value The value to set.
     * @return IfNotExists
     */
    public function ifNotExists(mixed $value): IfNotExists
    {
        return new IfNotExists($this->pathNode, $value);
    }

    /**
     * Creates an action to add a specified value to the attribute.
     *
     * @param mixed $value The value to add.
     * @return Action
     */
    public function add(mixed $value): Action
    {
        return Action::add($this->pathNode, $value);
    }

    /**
     * Creates an action to delete a specified value from the attribute.
     *
     * @param mixed $value The value to delete.
     * @return Action
     */
    public function delete(mixed $value): Action
    {
        return Action::delete($this->pathNode, $value);
    }

    /**
     * Creates an action to remove the attribute.
     * 
     * @return Action
     */
    public function remove(): Action
    {
        return Action::remove($this->pathNode);
    }

    /**
     * Creates an action to set the attribute to a specified value.
     *
     * @param Operation|Path|IfNotExists|mixed $value The value or operation to set.
     * @return  Action
     */
    public function set(mixed $value): Action
    {
        if ($value instanceof ExpressionOperandInterface) {

            $value = $value->toNode();
        }

        return Action::set($this->pathNode, $value);
    }

    /**
     * Returns parent path factory if parent path exists
     * 
     * @return Path|null
     */
    public function parent(): ?self
    {
        $parentNode = $this->pathNode->parent();

        return $parentNode ? new self($parentNode) : null;
    }

    /**
     * Returns child path factory for given segments
     * 
     * @param string|int ...$segments
     * @throws InvalidArgumentException
     */
    public function child(string|int ...$segments): self
    {
        return new self($this->pathNode->child($segments));
    }

    /**
     * Check if the current factory path is parent of other
     * 
     * @param Path $other
     * @return bool
     */
    public function isParentOf(Path $other): bool
    {
        return $this->pathNode->isParentOf($other->pathNode);
    }

    /**
     * Returns last segment for given path node
     * 
     * @return int|string
     */
    public function lastSegment(): int|string
    {
        return $this->pathNode->lastSegment();
    }

    /**
     * Returns JMESPath search expression
     * @param bool $resetIndexes
     * @return string
     */
    public function searchExpression(bool $resetIndexes = false): string
    {
        return $this->pathNode->searchExpression($resetIndexes);
    }

    /**
     * Returns a JMESPath search expression for the path within marshaled
     * DynamoDB data.
     *
     * The expression is relative to the contents of the root container, so its
     * DynamoDB type is not included. Nested map and list container types are
     * added before the corresponding path segments.
     *
     * When index resetting is enabled, every list index is replaced with zero.
     *
     * @param bool $resetIndexes
     * @return string
     */
    public function marshaledSearchExpression(bool $resetIndexes = false): string
    {
        return $this->pathNode->marshaledSearchExpression($resetIndexes);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->pathNode->__toString();
    }

    /**
     * @param string $attribute
     * @param string|int ...$segments
     * @throws InvalidArgumentException
     * @return Path
     */
    public static function create(string $attribute, string|int ...$segments): self
    {
        return new self(PathNode::create($attribute, ...$segments));
    }

    /**
     * Creates path from string.
     * - Dot (.) splits attribute segments.
     * - Brackets ([index]) denote list indexes.
     * - Double quotes (") wrap an attribute name to allow dots inside it, e.g.:
     *   attr1.attr2[3]."some.nested.attribute".attr4
     *   Inside quotes, use \" for a literal quote and \\ for a literal backslash.
     * 
     * @param string $pathString
     * @throws InvalidArgumentException
     * @return \DynaExp\Factories\Path
     */
    public static function fromString(string $pathString): Path
    {
        return new self(PathNode::fromString($pathString));
    }
}
