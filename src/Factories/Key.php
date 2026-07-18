<?php

namespace DynaExp\Factories;

use DynaExp\Builders\ProjectableInterface;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Path;
use DynaExp\Exceptions\InvalidArgumentException;

final class Key implements ProjectableInterface
{
    /**
     * @param Path $path
     */
    private function __construct(private Path $path)
    {
    }

    /**
     * @return Path
     */
    public function project(): Path
    {
        return $this->path;
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function equal(mixed $value): KeyCondition
    {
        return KeyCondition::equal($this->path, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThan(mixed $value): KeyCondition
    {
        return KeyCondition::lessThan($this->path, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThanEqual(mixed $value): KeyCondition
    {
        return KeyCondition::lessThanEqual($this->path, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThan(mixed $value): KeyCondition
    {
        return KeyCondition::greaterThan($this->path, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThanEqual(mixed $value): KeyCondition
    {
        return KeyCondition::greaterThanEqual($this->path, $value);
    }

    /**
     * @param mixed $prefix
     * @return KeyCondition
     */
    public function beginsWith(mixed $prefix): KeyCondition
    {
        return KeyCondition::beginsWith($this->path, $prefix);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return KeyCondition
     */
    public function between(mixed $lower, mixed $upper): KeyCondition
    {
        return KeyCondition::between($this->path, $lower, $upper);
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

        return new self(Path::create($attribute));
    }
}
