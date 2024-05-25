<?php

namespace DynaExp\Builders\Internal;

use DynaExp\Enums\UpdateOperationTypeEnum;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;
use DynaExp\Interfaces\TreeEvaluatorInterface;

class UpdateOperation implements TreeEvaluatorInterface
{
    /**
     * @var EvaluatedNodeInterface[]
     */
    private array $operations;

    /**
     * @param UpdateOperationTypeEnum $type
     */
    public function __construct(public UpdateOperationTypeEnum $type)
    {
        $this->operations = [];
    }

    /**
     * @param EvaluatedNodeInterface $updateOperation
     * @return void
     */
    public function add(EvaluatedNodeInterface $updateOperation)
    {
        $this->operations[] = $updateOperation;
    }

    /**
     * @return EvaluatedNodeInterface[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     *
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluateTree(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateUpdateOperation($this);
    }
}
