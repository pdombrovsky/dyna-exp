<?php

namespace DynaExp\Factories;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Path;
use InvalidArgumentException;

final class Key
{
    /**
     * @var Path
     */
    private Path $pathNode;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        if ($key === '') {
            throw new InvalidArgumentException("Key attribute name cannot be empty");
        }

        $this->pathNode = new Path([$key]);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function equal(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::equalKeyCond, $this->pathNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThan(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::lessThanKeyCond, $this->pathNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThanEqual(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::lessThanEqualKeyCond, $this->pathNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThan(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::greaterThanKeyCond, $this->pathNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThanEqual(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::greaterThanEqualKeyCond, $this->pathNode, $value);
    }

    /**
     * @param mixed $prefix
     * @return KeyCondition
     */
    public function beginsWith(mixed $prefix): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::beginsWithKeyCond, $this->pathNode, $prefix);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return KeyCondition
     */
    public function between(mixed $lower, mixed $upper): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::betweenKeyCond, $this->pathNode, $lower, $upper);
    }

    /**
     * @param string $attribute
     * @return \DynaExp\Factories\Key
     */
    public static function create(string $attribute): self
    {
        return new self($attribute);
    }
}
