<?php

namespace DynaExp\Factories;

use DynaExp\Builders\ProjectableInterface;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\PathNode;
use DynaExp\Exceptions\InvalidArgumentException;

final class Key implements ProjectableInterface
{
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
     * @param mixed $value
     * @return KeyCondition
     */
    public function equal(mixed $value): KeyCondition
    {
        return KeyCondition::equal($this->pathNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThan(mixed $value): KeyCondition
    {
        return KeyCondition::lessThan($this->pathNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThanEqual(mixed $value): KeyCondition
    {
        return KeyCondition::lessThanEqual($this->pathNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThan(mixed $value): KeyCondition
    {
        return KeyCondition::greaterThan($this->pathNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThanEqual(mixed $value): KeyCondition
    {
        return KeyCondition::greaterThanEqual($this->pathNode, $value);
    }

    /**
     * @param mixed $prefix
     * @return KeyCondition
     */
    public function beginsWith(mixed $prefix): KeyCondition
    {
        return KeyCondition::beginsWith($this->pathNode, $prefix);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return KeyCondition
     */
    public function between(mixed $lower, mixed $upper): KeyCondition
    {
        return KeyCondition::between($this->pathNode, $lower, $upper);
    }

    /**
     * @param string $attribute
     * @return \DynaExp\Factories\Key
     */
    public static function create(string $attribute): self
    {
        if ($attribute === '') {
            throw new InvalidArgumentException("Key attribute name cannot be empty");
        }

        return new self(PathNode::create($attribute));
    }
}
