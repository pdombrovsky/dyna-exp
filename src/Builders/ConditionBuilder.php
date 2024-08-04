<?php

namespace DynaExp\Builders;

use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Interfaces\BuilderInterface;
use DynaExp\Nodes\Condition;

final class ConditionBuilder implements BuilderInterface
{
    /**
     * @var Condition
     */
    private Condition $current;

    /**
     * @param Condition|ConditionBuilder|callable(callable(Condition $innerCondition):ConditionBuilder $innerBuilder): ConditionBuilder $condition
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
     * @param Condition ...$conditions
     */
    private function glueConditions(ConditionTypeEnum $glue, Condition ...$conditions): void
    {
        foreach ($conditions as $condition) {

            if (! $condition instanceof Condition) {

                $condition = static::parenthesizeInnerCondition($condition);
            }

            $this->current = new Condition($this->current, $glue, $condition);
        }
    }
 
    /**
     * @param ConditionBuilder|callable(callable(Condition $innerCondition):ConditionBuilder $innerBuilder): ConditionBuilder $condition
     */
    private static function parenthesizeInnerCondition(ConditionBuilder|callable $condition): Condition
    {
        if (is_callable($condition)) {

            $condition = $condition(fn(Condition $innerCondition) => new static($innerCondition));

        }

        return new Condition($condition->current, ConditionTypeEnum::parenthesesCond);
    }
    
    /**
     * @return Condition
     */
    public function build(): Condition
    {
        return $this->current;
    }
}
