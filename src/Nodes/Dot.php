<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;

final readonly class Dot implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $left
     * @param EvaluatedNodeInterface $right
     */
    public function __construct(public EvaluatedNodeInterface $left, public EvaluatedNodeInterface $right)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateDot($this);
    }
}
