<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\NodeEvaluatorInterface;

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
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(NodeEvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateIndex($this);
    }
}
