<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;

final readonly class Size implements EvaluableInterface
{
    /**
     * @param EvaluableInterface $node
     */
    public function __construct(public EvaluableInterface $node)
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
