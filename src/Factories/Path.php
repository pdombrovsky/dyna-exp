<?php

namespace DynaExp\Factories;

use DynaExp\Enums\AttributeTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Factories\IfNotExists;
use DynaExp\Factories\Size;
use DynaExp\Factories\Traits\ConditionTrait;
use DynaExp\Factories\Traits\OperationTrait;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\Path as PathNode;
use InvalidArgumentException;

final readonly class Path
{
    use ConditionTrait;
    use OperationTrait;

    public PathNode $pathNode;

    /**
     * Constructs a new Path instance.
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
            return "Attribute can not be empty string";
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
     * @param string $pathString
     * @throws InvalidArgumentException
     * @return Path
     */
    public static function fromString(string $pathString): Path
    {
        if ($pathString === '') {
            throw new InvalidArgumentException("Input string cannot be empty.");
        }

        $segments = [];
        $buffer = '';
        $prevoiusChar = '';
        $shouldProcessBuffer = false;
        $bracketLevel = 0;

        $length = strlen($pathString);
        $i = 0;

        while ($i < $length) {

            $char = $pathString[$i];

            switch ($char) {
                case '.':
                    if ($bracketLevel > 0) {
                        throw new InvalidArgumentException(sprintf("Invalid character '.' inside brackets. %s" , self::processedSymbolsMessage($pathString, $i)));
                    }
                    if ($buffer === '' && ']' !== $prevoiusChar) {
                        throw new InvalidArgumentException(sprintf("Empty attribute name found. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $shouldProcessBuffer = false;
                    break;
                case '[':
                    if ($bracketLevel > 0) {
                        throw new InvalidArgumentException(sprintf("Nested brackets are not allowed. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if (in_array($prevoiusChar, ['', '.'])) {
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
                    if (!ctype_digit($buffer)) {
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

            if (!$shouldProcessBuffer && $buffer !== '') {
                $segments[] = $buffer;
                $buffer = '';
            }

            $prevoiusChar = $char;
            $i++;
        }

        if ($bracketLevel !== 0) {
            throw new InvalidArgumentException(sprintf("Unmatched opening bracket. %s", self::processedSymbolsMessage($pathString, $i)));
        }

        if ($buffer) {
            $segments[] = $buffer;
        }

        $attribute = array_shift($segments);

        return new Path($attribute, ...$segments);
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

    /**
     * Creates a condition to check if the attribute exists.
     *
     * @return Condition
     */
    public function attributeExists(): Condition
    {
        return new Condition(ConditionTypeEnum::attrExistsCond,$this->pathNode);
    }

    /**
     * Creates a condition to check if the attribute does not exist.
     *
     * @return Condition
     */
    public function attributeNotExists(): Condition
    {
        return new Condition(ConditionTypeEnum::attrNotExistsCond,$this->pathNode);
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
     * Creates a condition to check if the attribute begins with a specified prefix.
     *
     * @param mixed $prefix The prefix to check.
     * @return Condition
     */
    public function beginsWith(mixed $prefix): Condition
    {
        return new Condition(ConditionTypeEnum::beginsWithCond,$this->pathNode, $prefix);
    }

    /**
     * Creates a condition to check if the attribute begins with a specified prefix.
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
}
