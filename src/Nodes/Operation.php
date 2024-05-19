<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\NodeEvaluatorInterface;

readonly class Operation implements EvaluatedNodeInterface
{
    /**
     * @param EvaluatedNodeInterface $node
     * @param OperationTypeEnum $type
     * @param mixed $value
     */
    public function __construct(public EvaluatedNodeInterface $node, public OperationTypeEnum $type, public mixed $value = null)
    {  
    }

    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(NodeEvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateOperation($this);
    }
}
