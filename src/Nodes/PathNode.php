<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluableInterface;
use DynaExp\Interfaces\EvaluatorInterface;

final readonly class PathNode implements EvaluableInterface
{
    /**
     * @param string|EvaluableInterface $left
     * @param null|int|EvaluableInterface $right
     */
    public function __construct(public string|EvaluableInterface $left, public null|int|EvaluableInterface $right = null)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluatePathNode($this);
    }
}
