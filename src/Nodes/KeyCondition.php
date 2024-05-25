<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;

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
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateKeyCondition($this);
    }
}
