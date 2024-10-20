<?php

namespace DynaExp\Builders;

use DynaExp\Builders\Internal\IfNotExists;
use DynaExp\Builders\Internal\NodeInterface;
use DynaExp\Builders\Internal\Size;
use DynaExp\Builders\Traits\ConditionTrait;
use DynaExp\Builders\Traits\NodeTrait;
use DynaExp\Builders\Traits\OperationTrait;
use DynaExp\Enums\AttributeTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\PathNode;

use InvalidArgumentException;
use Stringable;

final class Path implements NodeInterface, Stringable
{
    use NodeTrait;
    use ConditionTrait;
    use OperationTrait;

    /**
     * Constructs a new Path instance with the given attribute name.
     *
     * @param string $name The initial attribute name.
     * @throws InvalidArgumentException If the name is empty.
     */
    public function __construct(string $name)
    {
        if ($name === '') {
            throw new InvalidArgumentException("Attribute name cannot be empty");
        }

        $this->node = new PathNode($name);
    }

    /**
     * Adds a nested attribute segment to the path.
     *
     * @param string $segment The segment to add.
     * @return self
     * @throws InvalidArgumentException If the segment is empty.
     */
    public function segment(string $segment): self
    {
        if ($segment === '') {
            throw new InvalidArgumentException("Segment name cannot be empty");
        }

        $this->node = new PathNode($this->node, new PathNode($segment));

        return $this;
    }

    /**
     * Adds an index to the path, treating the current path as a list.
     *
     * @param int $index The index to add (zero-based).
     * @return self
     * @throws InvalidArgumentException If the index is negative.
     */
    public function index(int $index): self
    {
        if ($index < 0) {
            throw new InvalidArgumentException("Index must be a non-negative integer");
        }

        $this->node = new PathNode($this->node, $index);

        return $this;
    }

    /**
     * Combines the current path with another path.
     *
     * @param Path $path The path to add.
     * @return self
     */
    public function add(Path $path): self
    {
        $this->node = new PathNode($this->node, $path->node);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __tostring(): string
    {
        return $this->node->__toString();
    }

    /**
     * Creates a condition to check if the attribute exists.
     *
     * @return Condition
     */
    public function attributeExists(): Condition
    {
        return new Condition(ConditionTypeEnum::attrExistsCond,$this->node);
    }

    /**
     * Creates a condition to check if the attribute does not exist.
     *
     * @return Condition
     */
    public function attributeNotExists(): Condition
    {
        return new Condition(ConditionTypeEnum::attrNotExistsCond,$this->node);
    }

    /**
     * Creates a condition to check the type of the attribute.
     *
     * @param AttributeTypeEnum $type The expected attribute type.
     * @return Condition
     */
    public function attributeType(AttributeTypeEnum $type): Condition
    {
        return new Condition(ConditionTypeEnum::attrTypeCond, $this->node, $type->value);
    }

    /**
     * Creates a condition to check if the attribute begins with a specified prefix.
     *
     * @param mixed $prefix The prefix to check.
     * @return Condition
     */
    public function beginsWith(mixed $prefix): Condition
    {
        return new Condition(ConditionTypeEnum::beginsWithCond,$this->node, $prefix);
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
        return new Condition(ConditionTypeEnum::containsCond, $this->node, $value);
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
        return new Size($this);
    }

    /**
     * Sets a value if the attribute does not exist.
     *
     * @param mixed $value The value to set.
     * @return IfNotExists
     */
    public function ifNotExists(mixed $value): IfNotExists
    {
        return new IfNotExists($this, $value);
    }

    /**
     * Constructs a Path from the given segments.
     *
     * @param string        $attributeName The base attribute name.
     * @param string|int ...$segments      Additional path segments.
     * @return self
     */
    public static function fromSegments(string $attributeName, string|int ...$segments): self
    {
        $path = new self($attributeName);

        foreach ($segments as $part) {
            if (is_int($part)) {
                $path->index($part);
            } else {
                $path->segment($part);
            }
        }

        return $path;
    }

    /**
     * Parses a string representation of a path into a Path object.
     *
     * @param string $pathString The string to parse (e.g., "attribute.subAttribute[0]").
     * @return self
     * @throws InvalidArgumentException If the input string is invalid.
     */
    public static function fromString(string $pathString): self
    {
        if ($pathString === '') {
            throw new InvalidArgumentException("Input string cannot be empty.");
        }

        $parts = explode('.', $pathString);
        $path = null;

        foreach ($parts as $part) {
            if ($part === '') {
                throw new InvalidArgumentException("Empty attribute name found");
            }

            while ($part !== '') {
                if (str_starts_with($part, '[')) {
                    $endBracketPos = strpos($part, ']');
                    if ($endBracketPos === false) {
                        throw new InvalidArgumentException("Unclosed bracket found");
                    }

                    $number = substr($part, 1, $endBracketPos - 1);
                    if (!ctype_digit($number)) {
                        throw new InvalidArgumentException("Index must be a non-negative integer");
                    }

                    if ($path === null) {
                        throw new InvalidArgumentException("Index used without a preceding attribute name");
                    }

                    $path->index((int) $number);
                    $part = substr($part, $endBracketPos + 1);
                } else {
                    $nextBracketPos = strpos($part, '[');
                    $namePart = $nextBracketPos === false ? $part : substr($part, 0, $nextBracketPos);
                    $part = $nextBracketPos === false ? '' : substr($part, $nextBracketPos);

                    $path = $path?->segment($namePart) ?? new self($namePart);
                }
            }
        }

        return $path;
    }
}
