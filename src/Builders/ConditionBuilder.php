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
     * @param null|Condition|self $condition
     */
    public function __construct(null|Condition|self $condition = null)
    {
        $this->current = $condition instanceof self ? static::parenthesizeInnerCondition($condition->build()) : $condition;
    }

    /**
     * @param Condition|self ...$conditions
     * @return ConditionBuilder
     */
    public function and(Condition|self ...$conditions): ConditionBuilder
    {
        $this->glueConditions(ConditionTypeEnum::andCond, ...$conditions);

        return $this;
    }

    /**
     * @param Condition|self ...$conditions
     * @return ConditionBuilder
     */
    public function or(Condition|self ...$conditions): ConditionBuilder
    {
        $this->glueConditions(ConditionTypeEnum::orCond, ...$conditions);

        return $this;
    }

    /**
     * @param ConditionTypeEnum $glue
     * @param Condition|self ...$conditions
     */
    private function glueConditions(ConditionTypeEnum $glue, Condition|self ...$conditions): void
    {
        if (!$this->current) {

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
     * @param Condition $innerCondition
     */
    private static function parenthesizeInnerCondition(Condition $innerCondition): Condition
    {
        return new Condition(ConditionTypeEnum::parenthesesCond, $innerCondition);
    }

    /**
     * @return Condition
     */
    public function build(): Condition
    {
        return $this->current ?? throw new RuntimeException('There are no conditions to build.');;
    }
}
