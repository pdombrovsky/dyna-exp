<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;

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

    /**
     * @inheritDoc
     */
    public function convertToString(array $convertedNodes): string
    {
        return implode(' ', $convertedNodes);
    }
}
