<?php

namespace DynaExp\Builders;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\PathNode;

final class Key
{
    /**
     * @var PathNode
     */
    private PathNode $currentNode;
    
    /**
     * @param string $key
     */
    public function __construct(private string $key)
    {
        $this->currentNode = new PathNode($key);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function equal(mixed $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::equalKeyCond, [$value]);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThan(mixed $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::lessThanKeyCond, [$value]);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThanEqual(mixed $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::lessThanEqualKeyCond, [$value]);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThan(mixed $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::greaterThanKeyCond, [$value]);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThanEqual(mixed $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::greaterThanEqualKeyCond, [$value]);
    }

    /**
     * @param mixed $prefix
     * @return KeyCondition
     */
    public function beginsWith(mixed $prefix): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::beginsWithKeyCond, [$prefix]);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return KeyCondition
     */
    public function between(mixed $lower, mixed $upper): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::betweenKeyCond, [$lower, $upper]);
    }
}
