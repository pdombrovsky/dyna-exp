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
     * @param Condition|self $condition
     */
    public function __construct(Condition|self $condition)
    {
        $this->current = $condition instanceof Condition ? $condition : static::parenthesizeInnerCondition($condition->build());
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
        return $this->current;
    }
}
