<?php

namespace DynaExp\Builders;

use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Interfaces\TreeEvaluatorInterface;
use DynaExp\Interfaces\EvaluatorInterface;
use DynaExp\Nodes\Condition;

class ConditionBuilder implements TreeEvaluatorInterface
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
    public function and(Condition|ConditionBuilder|callable ...$conditions): static
    {
        foreach ($conditions as $condition) {

            if (! $condition instanceof Condition) {

                $condition = static::parenthesizeInnerCondition($condition);
            }

            $this->current = new Condition($this->current, ConditionTypeEnum::andCond, right: $condition);
        }

        return $this;
    }

    /**
     * @param Condition|ConditionBuilder|callable ...$conditions
     * @return ConditionBuilder
     */
    public function or(Condition ...$conditions): static
    {
        foreach ($conditions as $condition) {

            if (! $condition instanceof Condition) {

                $condition = static::parenthesizeInnerCondition($condition);
            }

            $this->current = new Condition($this->current, ConditionTypeEnum::orCond, right: $condition);
        }

        return $this;
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
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluateTree(EvaluatorInterface $nodeEvaluator): string
    {
        return $this->current->evaluate($nodeEvaluator);
    }
}
