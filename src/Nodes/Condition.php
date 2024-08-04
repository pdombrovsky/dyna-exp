<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatorInterface;
use DynaExp\Interfaces\EvaluableInterface;
use DynaExp\Enums\ConditionTypeEnum;

final readonly class Condition implements EvaluableInterface
{
    /**
     * @param EvaluableInterface $node
     * @param ConditionTypeEnum $type
     * @param null|array|EvaluableInterface $right
     */
    public function __construct(public EvaluableInterface $node, public ConditionTypeEnum $type, public null|array|EvaluableInterface $right = null)
    {
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateCondition($this);
    }
}
