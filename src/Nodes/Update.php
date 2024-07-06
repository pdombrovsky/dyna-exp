<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\UpdateOperationModeEnum;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;

final readonly class Update implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $left
     * @param UpdateOperationModeEnum $mode
     * @param ?EvaluatedNodeInterface $right
     * @param mixed $value
     */
    public function __construct(public EvaluatedNodeInterface $left, public UpdateOperationModeEnum $mode, public ?EvaluatedNodeInterface $right = null, public mixed $value = null)
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
