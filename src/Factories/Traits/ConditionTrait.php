<?php

namespace DynaExp\Factories\Traits;

use DynaExp\Nodes\Condition;
use DynaExp\Nodes\EvaluableInterface;

trait ConditionTrait
{
    abstract protected function evaluable(): EvaluableInterface;

    /**
     * Creates a condition to check if the attribute value is equal to the specified value.
     *
     * @param mixed $value The value to compare against.
     * @return Condition
     */
    public function equal(mixed $value): Condition
    {
        return Condition::equal($this->evaluable(), $value);
    }

    /**
     * Creates a condition to check if the attribute value is not equal to the specified value.
     *
     * @param mixed $value The value to compare against.
     * @return Condition
     */
    public function notEqual(mixed $value): Condition
    {
        return Condition::notEqual($this->evaluable(), $value);
    }

    /**
     * Creates a condition to check if the attribute value is less than the specified value.
     *
     * @param mixed $value The threshold value.
     * @return Condition
     */
    public function lessThan(mixed $value): Condition
    {
        return Condition::lessThan($this->evaluable(), $value);
    }

    /**
     * Creates a condition to check if the attribute value is less than or equal to the specified value.
     *
     * @param mixed $value The threshold value.
     * @return Condition
     */
    public function lessThanEqual(mixed $value): Condition
    {
        return Condition::lessThanEqual($this->evaluable(), $value);
    }

    /**
     * Creates a condition to check if the attribute value is greater than the specified value.
     *
     * @param mixed $value The threshold value.
     * @return Condition
     */
    public function greaterThan(mixed $value): Condition
    {
        return Condition::greaterThan($this->evaluable(), $value);
    }

    /**
     * Creates a condition to check if the attribute value is greater than or equal to the specified value.
     *
     * @param mixed $value The threshold value.
     * @return Condition
     */
    public function greaterThanEqual(mixed $value): Condition
    {
        return Condition::greaterThanEqual($this->evaluable(), $value);
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
        return Condition::between($this->evaluable(), $lower, $upper);
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
        return Condition::not($this->between($lower, $upper));
    }

    /**
     * Creates a condition to check if the attribute value is within the specified range of values.
     *
     * @param mixed $value The first value to check against.
     * @param mixed ...$range Additional values to check against.
     * @return Condition
     */
    public function in(mixed $value, mixed ...$range): Condition
    {
        return Condition::in($this->evaluable(), $value, ...$range);
    }

    /**
     * Creates a condition to check if the attribute value is not within the specified range of values.
     *
     * @param mixed $value The first value to check against.
     * @param mixed ...$range Additional values to check against.
     * @return Condition
     */
    public function notIn(mixed $value, mixed ...$range): Condition
    {
        return Condition::not($this->in($value, ...$range));
    }
}
