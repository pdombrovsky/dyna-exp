<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatorInterface;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Enums\ConditionTypeEnum;

readonly class Condition implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $node
     * @param EvaluatedNodeInterface $type
     * @param array $values
     * @param ?EvaluatedNodeInterface $right
     */
    public function __construct(public EvaluatedNodeInterface $node, public ConditionTypeEnum $type, public array $values = [], public ?EvaluatedNodeInterface $right = null)
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
