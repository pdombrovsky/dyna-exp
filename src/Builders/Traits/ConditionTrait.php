<?php

namespace DynaExp\Builders\Traits;

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
     * @param mixed $value
     * @return Condition
     */
    public function lessThan(mixed $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::lessThanCond, [$value]);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function lessThanEqual(mixed $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::lessThanEqualCond, [$value]);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function greaterThan(mixed $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::greaterThanCond, [$value]);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function greaterThanEqual(mixed $value): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::greaterThanEqualCond, [$value]);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return Condition
     */
    public function between(mixed $lower, mixed $upper): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::betweenCond, [$lower, $upper]);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return Condition
     */
    public function notBetween(mixed $lower, mixed $upper): Condition
    {
        return new Condition($this->between($lower, $upper), ConditionTypeEnum::notCond);
    }

    /**
     * @param mixed ...$range
     * @return Condition
     */
    public function in(mixed ...$range): Condition
    {
        return new Condition($this->getNode(), ConditionTypeEnum::inCond, $range);
    }

    /**
     * @param mixed ...$range
     * @return Condition
     */

    public function notIn(mixed ...$range): Condition
    {
        return new Condition($this->in($range), ConditionTypeEnum::notCond);
    }
}
