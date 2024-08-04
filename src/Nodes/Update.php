<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluableInterface;
use DynaExp\Interfaces\EvaluatorInterface;
use RangeException;


final readonly class Update implements EvaluableInterface
{
    /**
     * @param ActionsSequence[] $sequences
     */
    public function __construct(public array $sequences)
    { 
        if (empty($sequences)) {

            throw new RangeException("Action sequences must be set");
        }
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateUpdate($this);
    }
}
