<?php

namespace DynaExp\Factories;

use DynaExp\Builders\ProjectableInterface;
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
use DynaExp\Nodes\PathNode;
use Stringable;

final readonly class Path extends AbstractNode implements Stringable, ProjectableInterface
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
     * @return \DynaExp\Nodes\PathNode
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
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->pathNode->__toString();
    }

    /**
     * @param string $attribute
     * @param string|int ...$segments
     * @throws \DynaExp\Exceptions\InvalidArgumentException
     * @return Path
     */
    public static function create(string $attribute, string|int ...$segments): self
    {
        return new Path(PathNode::create($attribute, ...$segments));
    }

    /**
     * Creates path from string.
     * - Dot (.) splits attribute segments.
     * - Brackets ([index]) denote list indexes.
     * - Double quotes (") wrap an attribute name to allow dots inside it, e.g.:
     *   attr1.attr2[3]."some.nested.attribute".attr4
     *   Inside quotes, use \" to include a double quote character; a backslash is otherwise literal.
     * 
     * @param string $pathString
     * @throws InvalidArgumentException
     * @throws \DynaExp\Exceptions\RuntimeException
     * @return \DynaExp\Factories\Path
     */
    public static function fromString(string $pathString): Path
    {
        if ($pathString === '') {
            throw new InvalidArgumentException("Input string cannot be empty.");
        }

        $segments = [];
        $buffer = '';
        $previousChar = '';
        $shouldProcessBuffer = false;
        $bracketLevel = 0;
        $inQuotes = false;
        $awaitingAttribute = false;
        $lastDotIndex = -1;

        $length = strlen($pathString);
        $i = 0;

        while ($i < $length) {

            $char = $pathString[$i];

            // Handle quoted attribute names
            if ($inQuotes) {
                if ($char === '\\') {
                    $next = $pathString[$i + 1] ?? null;
                    if ($next === '"') {
                        // escape sequence for a quote inside quotes: \"
                        $buffer .= '"';
                        $previousChar = '"';
                        $i += 2;
                        continue;
                    }
                    // literal backslash
                    $buffer .= '\\';
                    $previousChar = '\\';
                    $i++;
                    continue;
                }
                if ($char === '"') {
                    // close quotes, keep buffer until next delimiter
                    $inQuotes = false;
                    $previousChar = '"';
                    $i++;
                    continue;
                }
                $buffer .= $char;
                $previousChar = $char;
                $i++;
                continue;
            }

            switch ($char) {
                case '.':
                    if ($bracketLevel > 0) {
                        throw new InvalidArgumentException(sprintf("Invalid character '.' inside brackets. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if ($buffer === '' && ! in_array($previousChar, [']', '"'], true)) {
                        throw new InvalidArgumentException(sprintf("Empty attribute name found. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $shouldProcessBuffer = false;
                    $awaitingAttribute = true;
                    $lastDotIndex = $i;
                    break;
                case '"':
                    if ($bracketLevel > 0) {
                        throw new InvalidArgumentException(sprintf("Quoted attribute name is not allowed inside brackets. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if ($buffer !== '') {
                        throw new InvalidArgumentException(sprintf("Unexpected '\"' inside attribute name. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if (! in_array($previousChar, ['', '.'], true)) {
                        throw new InvalidArgumentException(sprintf("Quoted attribute must start at beginning or after a dot. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $inQuotes = true;
                    $shouldProcessBuffer = true;
                    $awaitingAttribute = false;
                    break;
                case '[':
                    if ($bracketLevel > 0) {
                        throw new InvalidArgumentException(sprintf("Nested brackets are not allowed. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if (in_array($previousChar, ['', '.'])) {
                        throw new InvalidArgumentException(sprintf("Index used without a preceding attribute name. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $bracketLevel++;
                    $shouldProcessBuffer = false;
                    break;
                case ']':
                    if ($bracketLevel === 0) {
                        throw new InvalidArgumentException(sprintf("Unmatched closing bracket. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if ($buffer === '') {
                        throw new InvalidArgumentException(sprintf("Empty index found. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if (! ctype_digit($buffer)) {
                        throw new InvalidArgumentException(sprintf("Only non-negative integers are allowed in index, '$buffer' given. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $buffer = (int) $buffer;

                    $bracketLevel--;
                    $shouldProcessBuffer = false;
                    break;
                default:
                    $buffer .= $char;
                    $shouldProcessBuffer = true;
                    $awaitingAttribute = false;
            }

            if (! $shouldProcessBuffer && $buffer !== '') {
                $segments[] = $buffer;
                $buffer = '';
            }

            $previousChar = $char;
            $i++;
        }

        if ($awaitingAttribute) {
            throw new InvalidArgumentException(sprintf("Empty attribute name found. %s", self::processedSymbolsMessage($pathString, $lastDotIndex)));
        }

        if ($inQuotes) {
            throw new InvalidArgumentException(sprintf("Unmatched quote. %s", self::processedSymbolsMessage($pathString, $i)));
        }

        if ($bracketLevel !== 0) {
            throw new InvalidArgumentException(sprintf("Unmatched opening bracket. %s", self::processedSymbolsMessage($pathString, $i)));
        }

        if ($buffer) {
            $segments[] = $buffer;
        }

        return new Path(new PathNode($segments));
    }

    /**
     * @param string $pathString
     * @param int $index
     * @return string
     */
    private static function processedSymbolsMessage(string $pathString, int $index): string
    {
        return sprintf("Processed symbols: '%s'.", substr($pathString, 0, $index));
    }
}
