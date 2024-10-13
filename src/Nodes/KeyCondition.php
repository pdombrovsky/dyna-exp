<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;

final readonly class KeyCondition implements EvaluableInterface
{
    /**
     * @param EvaluableInterface $node
     * @param KeyConditionTypeEnum $type
     * @param array|EvaluableInterface $right
     */
    public function __construct(public EvaluableInterface $node, public KeyConditionTypeEnum $type, public array|EvaluableInterface $right)
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
