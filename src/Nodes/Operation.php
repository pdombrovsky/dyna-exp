<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;

final readonly class Operation implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

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

    /**
     * @inheritDoc
     */
    public function convertToString(array $nodes): string
    {
        if (OperationTypeEnum::listPrepend == $this->type) {

            $nodes = array_reverse($nodes);

            $fmtString = OperationTypeEnum::listAppend->value;

        }
        else {
            
            $fmtString = $this->type->value;
        }

        return sprintf($fmtString, ...$nodes);
    }
}
