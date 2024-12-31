<?php

namespace DynaExp\Factories\Traits;

use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Condition;

trait ConditionTrait
{
    /**
     * Creates a condition to check if the attribute value is equal to the specified value.
     *
     * @param mixed $value The value to compare against.
     * @return Condition
     */
    public function equal(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::equalCond, $this->pathNode, $value);
    }

    /**
     * Creates a condition to check if the attribute value is not equal to the specified value.
     *
     * @param mixed $value The value to compare against.
     * @return Condition
     */
    public function notEqual(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::notEqualCond, $this->pathNode, $value);
    }

    /**
     * Creates a condition to check if the attribute value is less than the specified value.
     *
     * @param mixed $value The threshold value.
     * @return Condition
     */
    public function lessThan(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::lessThanCond,$this->pathNode, $value);
    }

    /**
     * Creates a condition to check if the attribute value is less than or equal to the specified value.
     *
     * @param mixed $value The threshold value.
     * @return Condition
     */
    public function lessThanEqual(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::lessThanEqualCond, $this->pathNode, $value);
    }

    /**
     * Creates a condition to check if the attribute value is greater than the specified value.
     *
     * @param mixed $value The threshold value.
     * @return Condition
     */
    public function greaterThan(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::greaterThanCond, $this->pathNode, $value);
    }

    /**
     * Creates a condition to check if the attribute value is greater than or equal to the specified value.
     *
     * @param mixed $value The threshold value.
     * @return Condition
     */
    public function greaterThanEqual(mixed $value): Condition
    {
        return new Condition(ConditionTypeEnum::greaterThanEqualCond, $this->pathNode, $value);
    }

    /**
     * Creates a condition to check if the attribute value is between the specified lower and upper bounds.
     *
     * @param mixed $lower The lower bound of the range.
     * @param mixed $upper The upper bound of the range.
     * @return Condition
     */
    public function between(mixed $lower, mixed $upper): Condition
    {
        return new Condition(ConditionTypeEnum::betweenCond, $this->pathNode, $lower, $upper);
    }

    /**
     * Creates a condition to check if the attribute value is not between the specified lower and upper bounds.
     *
     * @param mixed $lower The lower bound of the range.
     * @param mixed $upper The upper bound of the range.
     * @return Condition
     */
    public function notBetween(mixed $lower, mixed $upper): Condition
    {
        return new Condition(ConditionTypeEnum::notCond, $this->between($lower, $upper));
    }

    /**
     * Creates a condition to check if the attribute value is within the specified range of values.
     *
     * @param mixed ...$range The set of values to check against.
     * @return Condition
     */
    public function in(mixed ...$range): Condition
    {
        return new Condition(ConditionTypeEnum::inCond, $this->pathNode, ...$range);
    }

    /**
     * Creates a condition to check if the attribute value is not within the specified range of values.
     *
     * @param mixed ...$range The set of values to check against.
     * @return Condition
     */

    public function notIn(mixed ...$range): Condition
    {
        return new Condition(ConditionTypeEnum::notCond, $this->in($range));
    }
}
