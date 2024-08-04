<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluableInterface;
use DynaExp\Interfaces\EvaluatorInterface;
use RangeException;

final readonly class Projection implements EvaluableInterface
{
    /**
     * @param EvaluableInterface[] $attributes
     */
    public function __construct(public array $attributes)
    { 
        if (empty($sequences)) {

            throw new RangeException("Projected attributes must be set");
        }
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
