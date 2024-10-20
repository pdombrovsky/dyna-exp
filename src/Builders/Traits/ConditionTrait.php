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
        return new Condition(ConditionTypeEnum::equalCond,$this->getNode(), $value);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function notEqual(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::notEqualCond,$this->getNode(), $value);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function lessThan(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::lessThanCond,$this->getNode(), $value);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function lessThanEqual(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::lessThanEqualCond, $this->getNode(), $value);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function greaterThan(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::greaterThanCond,$this->getNode(), $value);
    }

    /**
     * @param mixed $value
     * @return Condition
     */
    public function greaterThanEqual(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::greaterThanEqualCond,$this->getNode(), $value);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return Condition
     */
    public function between(mixed $lower, mixed $upper): Condition
    {
        return new Condition(ConditionTypeEnum::betweenCond, $this->getNode(), $lower, $upper);
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @return Condition
     */
    public function notBetween(mixed $lower, mixed $upper): Condition
    {
        return new Condition(ConditionTypeEnum::notCond, $this->between($lower, $upper));
    }

    /**
     * @param mixed ...$range
     * @return Condition
     */
    public function in(mixed ...$range): Condition
    {
        return new Condition(ConditionTypeEnum::inCond, $this->getNode(), ...$range);
    }

    /**
     * @param mixed ...$range
     * @return Condition
     */

    public function notIn(mixed ...$range): Condition
    {
        return new Condition(ConditionTypeEnum::notCond, $this->in($range));
    }
}
