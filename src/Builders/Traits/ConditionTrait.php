<?php

namespace DynaExp\Builders\Traits;

use Aws\DynamoDb\BinaryValue;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Condition;

trait ConditionTrait
{
    /**
     * @param mixed $value
     * @return Condition
     */
    public function equal(mixed $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::equalCond, [$value]);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function notEqual(mixed $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::notEqualCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $value
     * @return Condition
     */
    public function lessThan(int|float|string|BinaryValue $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::lessThanCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $value
     * @return Condition
     */
    public function lessThanEqual(int|float|string|BinaryValue $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::lessThanEqualCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $value
     * @return Condition
     */
    public function greaterThan(int|float|string|BinaryValue $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::greaterThanCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $value
     * @return Condition
     */
    public function greaterThanEqual(int|float|string|BinaryValue $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::greaterThanEqualCond, [$value]);
    }

    /**
     * @param int|float|string|BinaryValue $lower
     * @param int|float|string|BinaryValue $upper
     * @return Condition
     */
    public function between(int|float|string|BinaryValue $lower, int|float|string|BinaryValue $upper): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::betweenCond, [$lower, $upper]);
    }

    /**
     * @param int|float|string|BinaryValue ...$range
     * @return Condition
     */
    public function in(int|float|string|BinaryValue ...$range): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::inCond, $range);
    } 
}
