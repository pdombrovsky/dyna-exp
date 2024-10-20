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
        return new KeyCondition(KeyConditionTypeEnum::equalKeyCond, $this->currentNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThan(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::lessThanKeyCond, $this->currentNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function lessThanEqual(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::lessThanEqualKeyCond, $this->currentNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThan(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::greaterThanKeyCond, $this->currentNode, $value);
    }

    /**
     * @param mixed $value
     * @return KeyCondition
     */
    public function greaterThanEqual(mixed $value): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::greaterThanEqualKeyCond, $this->currentNode, $value);
    }

    /**
     * @param mixed $prefix
     * @return KeyCondition
     */
    public function beginsWith(mixed $prefix): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::beginsWithKeyCond, $this->currentNode, $prefix);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return KeyCondition
     */
    public function between(mixed $lower, mixed $upper): KeyCondition
    {
        return new KeyCondition(KeyConditionTypeEnum::betweenKeyCond, $this->currentNode, $lower, $upper);
    }
}
