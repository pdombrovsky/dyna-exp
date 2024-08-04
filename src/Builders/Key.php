<?php

namespace DynaExp\Builders;

use Aws\DynamoDb\BinaryValue;
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
     * @param int|float|string|BinaryValue $value
     * @return KeyCondition
     */
    public function equal(int|float|string|BinaryValue $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::equalKeyCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $value
     * @return KeyCondition
     */
    public function lessThan(int|float|string|BinaryValue $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::lessThanKeyCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $value
     * @return KeyCondition
     */
    public function lessThanEqual(int|float|string|BinaryValue $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::lessThanEqualKeyCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $value
     * @return KeyCondition
     */
    public function greaterThan(int|float|string|BinaryValue $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::greaterThanKeyCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $value
     * @return KeyCondition
     */
    public function greaterThanEqual(int|float|string|BinaryValue $value): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::greaterThanEqualKeyCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $prefix
     * @return KeyCondition
     */
    public function beginsWith(int|float|string|BinaryValue $prefix): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::beginsWithKeyCond, [$prefix]);
    }

    /**
     * @param int|float|string|BinaryValue $lower
     * @param int|float|string|BinaryValue $upper
     * @return KeyCondition
     */
    public function between(int|float|string|BinaryValue $lower, int|float|string|BinaryValue $upper): KeyCondition
    {
        return new KeyCondition($this->currentNode, KeyConditionTypeEnum::betweenKeyCond, [$lower, $upper]);
    }
}
