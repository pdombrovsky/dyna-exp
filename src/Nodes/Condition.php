<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\NodeEvaluatorInterface;
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
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(NodeEvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateCondition($this);
    }
}
