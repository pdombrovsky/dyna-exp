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
use Stringable;

final readonly class Path extends AbstractNode implements Stringable
{
    use ConditionTrait;
    use OperationTrait;

    private PathNode $pathNode;

    /**
     * @param array<string|int> $segments
     * @throws InvalidArgumentException
     */
    private function __construct(array $segments)
    {
        $this->pathNode = new PathNode($segments);
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

    /**
     * Returns parent path factory if exists
     * 
     * @return Path|null
     */
    public function parent(): ?self
    {
        $segments = $this->pathNode->parentPathSegments();

        return $segments ? new self($segments) : null;
    }

    /**
     * Returns child path factory for given segments
     * 
     * @param string|int ...$segments
     * @throws InvalidArgumentException
     */
    public function child(string|int ...$segments): self
    {
        self::validateSegments(...$segments);

        return new self([...$this->pathNode->segments, ...$segments]);
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
     * 
     * @return string
     */
    public function searchExpression(): string
    {
        $segments = array_map(
            fn(string|int $segment) => is_int($segment) ? $segment : "\"$segment\"",
            $this->pathNode->segments
        );

        return $this->pathNode->convertToString($segments);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->pathNode->__toString();
    }

    /**
     * @param string|int ...$segments
     * @throws \DynaExp\Exceptions\InvalidArgumentException
     * @return Path
     */
    public static function create(string|int ...$segments): self
    {
        self::validateSegments(...$segments);

        return new Path($segments);
    }

    /**
     * @param string|int ...$segments
     * @throws \DynaExp\Exceptions\InvalidArgumentException
     * @return void
     */
    private static function validateSegments(string|int ...$segments): void
    {
        $validationMessage = self::getValidationMessage(...$segments);

        if ($validationMessage) {

            throw new InvalidArgumentException($validationMessage);
        }
    }

    /**
     * @param string|int ...$segments
     * @return string
     */
    private static function getValidationMessage(string|int ...$segments): string
    {
        if (empty($segments)) {
            return "Segments must not be empty.";
        }

        $attribute = array_shift($segments);

        if (! is_string($attribute) || $attribute === '') {

            return "First segment must be not empty string.";
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
     * Creates path from string.
     * Any dot will be treated as segments splitter.
     * Any square bracket will be treated as index description.
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

        $length = strlen($pathString);
        $i = 0;

        while ($i < $length) {

            $char = $pathString[$i];

            switch ($char) {
                case '.':
                    if ($bracketLevel > 0) {
                        throw new InvalidArgumentException(sprintf("Invalid character '.' inside brackets. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if ($buffer === '' && ']' !== $previousChar) {
                        throw new InvalidArgumentException(sprintf("Empty attribute name found. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $shouldProcessBuffer = false;
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
            }

            if (! $shouldProcessBuffer && $buffer !== '') {
                $segments[] = $buffer;
                $buffer = '';
            }

            $previousChar = $char;
            $i++;
        }

        if ($bracketLevel !== 0) {
            throw new InvalidArgumentException(sprintf("Unmatched opening bracket. %s", self::processedSymbolsMessage($pathString, $i)));
        }

        if ($buffer) {
            $segments[] = $buffer;
        }

        return new Path($segments);
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
