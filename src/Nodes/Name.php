<?php

namespace DynaExp\Nodes;

use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;

final readonly class Name implements EvaluatedNodeInterface
{
    /**
     * @param string $name
     */
    public function __construct(public string $name)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateName($this);
    }
}
