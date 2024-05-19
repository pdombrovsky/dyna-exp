<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\NodeEvaluatorInterface;

readonly class Dot implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $left
     * @param EvaluatedNodeInterface $right
     */
    public function __construct(public EvaluatedNodeInterface $left, public EvaluatedNodeInterface $right)
    {  
    }

    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(NodeEvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateDot($this);
    }
}
