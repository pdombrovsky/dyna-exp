<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;

final readonly class Projection implements EvaluableInterface
{
    /**
     * @param EvaluableInterface[] $attributes
     */
    public function __construct(public array $attributes)
    { 
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateProjection($this);
    }
}
