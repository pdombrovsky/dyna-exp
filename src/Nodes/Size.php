<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\NodeEvaluatorInterface;

readonly class Size implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $node
     */
    public function __construct(public EvaluatedNodeInterface $node)
    {  
    }

    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(NodeEvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateSize($this);
    }
}
