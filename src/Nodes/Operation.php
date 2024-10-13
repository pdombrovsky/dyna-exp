<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;

final readonly class Operation implements EvaluableInterface
{
    /**
     * @param EvaluableInterface $node
     * @param OperationTypeEnum $type
     * @param mixed $value
     */
    public function __construct(public EvaluableInterface $node, public OperationTypeEnum $type, public mixed $value = null)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateOperation($this);
    }
}
