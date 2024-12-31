<?php

namespace DynaExp\Factories;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Enums\AttributeTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Factories\Abstracts\AbstractNode;
use DynaExp\Factories\IfNotExists;
use DynaExp\Factories\Size;
use DynaExp\Factories\Traits\ConditionTrait;
use DynaExp\Factories\Traits\OperationTrait;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Path as PathNode;

final readonly class Path extends AbstractNode
{
    use ConditionTrait;
    use OperationTrait;

    private PathNode $pathNode;

    /**
     * @param string $attribute
     * @param string|int ...$segments
     * @throws InvalidArgumentException
     */
    public function __construct(string $attribute, string|int ...$segments)
    {
        $validationMessage = static::validatePath($attribute, ...$segments);

        if ($validationMessage) {

            throw new InvalidArgumentException($validationMessage);
        }

        $this->pathNode = new PathNode([$attribute, ...$segments]);
    }

    /**
     * @param string $attribute
     * @param string|int ...$segments
     * @return string
     */
    private static function validatePath(string $attribute, string|int ...$segments): string
    {
        if ($attribute === '') {
            return "Attribute can not be empty string.";
        }

        $checkedSegments = [];
        $errorMessage = '';
        foreach ($segments as $segment) {

            if (is_int($segment) && $segment < 0) {

                $errorMessage = "Index can not be negative, '$segment' given.";
                break;
            }

            if ($segment === '') {

                $errorMessage = 'Path segment can not be empty string.';
                break;
            }

            $checkedSegments[] = $segment;
        }

        if ($errorMessage) {

            $checked = (new PathNode([$attribute, ...$checkedSegments]))->__tostring();

            return "Wrong path segment found after: '$checked'. $errorMessage";
        }

        return '';
    }

    /**
     * @return \DynaExp\Nodes\Path
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
        return new Condition(ConditionTypeEnum::attrExistsCond, $this->pathNode);
    }

    /**
     * Creates a condition to check if the attribute does not exist.
     *
     * @return Condition
     */
    public function attributeNotExists(): Condition
    {
        return new Condition(ConditionTypeEnum::attrNotExistsCond, $this->pathNode);
    }

    /**
     * Creates a condition to check the type of the attribute.
     *
     * @param AttributeTypeEnum $type The expected attribute type.
     * @return Condition
     */
    public function attributeType(AttributeTypeEnum $type): Condition
    {
        return new Condition(ConditionTypeEnum::attrTypeCond, $this->pathNode, $type->value);
    }

    /**
     * Creates a condition to ensure the attribute type is not the specified type.
     *
     * @param AttributeTypeEnum $type The expected attribute type.
     * @return Condition
     */
    public function attributeTypeNot(AttributeTypeEnum $type): Condition
    {
        return new Condition(ConditionTypeEnum::notCond, $this->attributeType($type));
    }

    /**
     * Creates a condition to check if the attribute begins with a specified prefix.
     *
     * @param mixed $prefix The prefix to check.
     * @return Condition
     */
    public function beginsWith(mixed $prefix): Condition
    {
        return new Condition(ConditionTypeEnum::beginsWithCond, $this->pathNode, $prefix);
    }

    /**
     * Creates a condition to check if the attribute not begins with a specified prefix.
     *
     * @param mixed $prefix The prefix to check.
     * @return Condition
     */
    public function notBeginsWith(mixed $prefix): Condition
    {
        return new Condition(ConditionTypeEnum::notCond, $this->beginsWith($prefix));
    }

    /**
     * Creates a condition to check if the attribute contains a specified value.
     *
     * @param mixed $value The value to check for containment.
     * @return Condition
     */
    public function contains(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::containsCond, $this->pathNode, $value);
    }

    /**
     * Creates a condition to check if the attribute not contains a specified value.
     *
     * @param mixed $value The value to check for containment.
     * @return Condition
     */
    public function notContains(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::notCond, $this->contains($value));
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
        return new Action(ActionTypeEnum::add, $this->pathNode, $value);
    }

    /**
     * Creates an action to delete a specified value from the attribute.
     *
     * @param mixed $value The value to delete.
     * @return Action
     */
    public function delete(mixed $value): Action
    {
        return new Action(ActionTypeEnum::delete, $this->pathNode, $value);
    }

    /**
     * Creates an action to remove the attribute.
     * 
     * @return Action
     */
    public function remove(): Action
    {
        return new Action(ActionTypeEnum::remove, $this->pathNode);
    }

    /**
     * Creates an action to set the attribute to a specified value.
     *
     * @param Operation|Path|IfNotExists|mixed $value The value or operation to set.
     * @return  Action
     */
    public function set(mixed $value): Action
    {
        if ($value instanceof AbstractNode) {

            $value = $value->getNode();
        }

        return new Action(ActionTypeEnum::set, $this->pathNode, $value);
    }
}
