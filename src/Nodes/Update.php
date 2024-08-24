<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluableInterface;
use DynaExp\Interfaces\EvaluatorInterface;


final readonly class Update implements EvaluableInterface
{
    /**
     * @param ActionsSequence[] $sequences
     */
    public function __construct(public array $sequences)
    { 
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
