<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;

readonly class Size implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $node
     */
    public function __construct(public EvaluatedNodeInterface $node)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateSize($this);
    }
}
