<?php

namespace DynaExp\Builders;

use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Exceptions\RuntimeException;
use DynaExp\Nodes\Condition;

final class ConditionBuilder
{
    /**
     * @var null|Condition
     */
    private null|Condition $current;

    /**
     * Initializes a new ConditionBuilder.
     * If the provided argument is another ConditionBuilder, its result is considered nested
     * and will be automatically wrapped in parentheses.
     *
     * @param null|Condition|self $condition An initial Condition or ConditionBuilder to start with, or null if none.
     */
    public function __construct(null|Condition|self $condition = null)
    {
        $this->current = $condition instanceof self
            ? static::parenthesizeInnerCondition($condition->build())
            : $condition;
    }
    
    /**
     * Creates a builder that ANDs the provided conditions.
     *
     * @param Condition|self ...$conditions
     * @return ConditionBuilder
     */
    public static function allOf(Condition|self ...$conditions): ConditionBuilder
    {
        $builder = new self();
        $builder->and(...$conditions);
        return $builder;
    }

    /**
     * Creates a builder that ORs the provided conditions.
     *
     * @param Condition|self ...$conditions
     * @return ConditionBuilder
     */
    public static function anyOf(Condition|self ...$conditions): ConditionBuilder
    {
        $builder = new self();
        $builder->or(...$conditions);
        return $builder;
    }

    /**
     * Appends one or more conditions with a logical AND operator.
     * If no initial condition is set, at least two conditions are required.
     *
     * If one of the provided arguments is another ConditionBuilder,
     * the resulting expression from that builder is considered nested
     * and will be automatically wrapped in parentheses.
     *
     * @param Condition|self ...$conditions One or more Condition or ConditionBuilder instances to chain with AND.
     * @return ConditionBuilder Returns the current builder instance for a fluent interface.
     */
    public function and(Condition|self ...$conditions): ConditionBuilder
    {
        $this->glueConditions(ConditionTypeEnum::andCond, ...$conditions);

        return $this;
    }

    /**
     * Appends one or more conditions with a logical OR operator.
     * If no initial condition is set, at least two conditions are required.
     *
     * If one of the provided arguments is another ConditionBuilder,
     * the resulting expression from that builder is considered nested
     * and will be automatically wrapped in parentheses.
     *
     * @param Condition|self ...$conditions One or more Condition or ConditionBuilder instances to chain with OR.
     * @return ConditionBuilder Returns the current builder instance for a fluent interface.
     */
    public function or(Condition|self ...$conditions): ConditionBuilder
    {
        $this->glueConditions(ConditionTypeEnum::orCond, ...$conditions);

        return $this;
    }

    /**
     * Combines the current condition with the provided conditions using the given ConditionTypeEnum ($glue).
     * If no initial condition is set, at least two conditions are required.
     *
     * If one of the provided arguments is another ConditionBuilder,
     * the resulting expression from that builder is considered nested
     * and will be automatically wrapped in parentheses.
     *
     * @param ConditionTypeEnum $glue The logical operator to use (e.g., AND or OR).
     * @param Condition|self ...$conditions Additional conditions or builders to combine.
     * @throws RuntimeException If no initial condition is set and fewer than two conditions are provided.
     */
    private function glueConditions(ConditionTypeEnum $glue, Condition|self ...$conditions): void
    {
        if (! $this->current) {
            if (count($conditions) < 2) {
                throw new RuntimeException('At least two conditions required if initial condition is not set.');
            }

            $condition = array_shift($conditions);

            if ($condition instanceof ConditionBuilder) {
                $condition = static::parenthesizeInnerCondition($condition->build());
            }

            $this->current = $condition;
        }

        foreach ($conditions as $condition) {
            if ($condition instanceof ConditionBuilder) {
                $condition = static::parenthesizeInnerCondition($condition->build());
            }

            $this->current = new Condition($glue, $this->current, $condition);
        }
    }

    /**
     * Wraps the given condition in parentheses by creating a new Condition
     * with the type `parenthesesCond` and a single operand.
     *
     * @param Condition $innerCondition The condition to be wrapped in parentheses.
     * @return Condition A new Condition object that encloses $innerCondition in parentheses.
     */
    private static function parenthesizeInnerCondition(Condition $innerCondition): Condition
    {
        return new Condition(ConditionTypeEnum::parenthesesCond, $innerCondition);
    }

    /**
     * Constructs and returns the final Condition object.
     * If no conditions have been added, a RuntimeException is thrown.
     *
     * @return Condition The resulting Condition object representing all combined conditions.
     * @throws RuntimeException If there are no conditions to build.
     */
    public function build(): Condition
    {
        return $this->current ?? throw new RuntimeException('There are no conditions to build.');
    }
}
