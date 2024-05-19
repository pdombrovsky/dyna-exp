<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\NodeEvaluatorInterface;

readonly class KeyCondition implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $node
     * @param KeyConditionTypeEnum $type
     * @param array $values
     * @param ?EvaluatedNodeInterface $right
     */
    public function __construct(public EvaluatedNodeInterface $node, public KeyConditionTypeEnum $type, public array $values = [], public ?EvaluatedNodeInterface $right = null)
    {
    }

    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(NodeEvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateKeyCondition($this);
    }
}
