<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;

readonly class Index implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $node
     * @param int $value
     */
    public function __construct(public EvaluatedNodeInterface $node, public int $value)
    {
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateIndex($this);
    }
}
