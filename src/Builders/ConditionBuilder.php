<?php

namespace DynaExp\Builders;

use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Condition;

final class ConditionBuilder
{
    /**
     * @var Condition
     */
    private Condition $current;

    /**
     * @param Condition|ConditionBuilder|callable $condition
     */
    public function __construct(Condition|ConditionBuilder|callable $condition)
    {
        $this->current = ($condition instanceof Condition) ?
            $condition :
            static::parenthesizeInnerCondition($condition);
    }

    /**
     * @param Condition|ConditionBuilder|callable ...$conditions
     * @return ConditionBuilder
     */
    public function and(Condition|ConditionBuilder|callable ...$conditions): ConditionBuilder
    {
        $this->glueConditions(ConditionTypeEnum::andCond, ...$conditions);

        return $this;
    }

    /**
     * @param Condition|ConditionBuilder|callable ...$conditions
     * @return ConditionBuilder
     */
    public function or(Condition|ConditionBuilder|callable ...$conditions): ConditionBuilder
    {
        $this->glueConditions(ConditionTypeEnum::orCond, ...$conditions);

        return $this;
    }

    /**
     * @param ConditionTypeEnum $glue
     * @param Condition|ConditionBuilder|callable ...$conditions
     */
    private function glueConditions(ConditionTypeEnum $glue, Condition|ConditionBuilder|callable ...$conditions): void
    {
        foreach ($conditions as $condition) {

            if (! $condition instanceof Condition) {

                $condition = static::parenthesizeInnerCondition($condition);
            }

            $this->current = new Condition($glue, $this->current, $condition);
        }
    }
 
    /**
     * @param ConditionBuilder|callable $condition
     */
    private static function parenthesizeInnerCondition(ConditionBuilder|callable $condition): Condition
    {
        if (is_callable($condition)) {

            $condition = $condition(fn(Condition $innerCondition) => new self($innerCondition));

        }

        return new Condition(ConditionTypeEnum::parenthesesCond, $condition->current);
    }

    /**
     * @return Condition
     */
    public function build(): Condition
    {
        return $this->current;
    }
}
