<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;

final readonly class Operation implements EvaluableInterface
{
    /**
     * @var array<EvaluableInterface|mixed>
     */
    public array $nodes;

    /**
     * @param OperationTypeEnum $type
     * @param mixed ...$nodes
     */
    public function __construct(public OperationTypeEnum $type, mixed ...$nodes)
    { 
        $this->nodes = $nodes;
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
