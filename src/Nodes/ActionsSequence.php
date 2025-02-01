<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;

final readonly class ActionsSequence implements EvaluableInterface
{
    /**
     * @param ActionTypeEnum $actionType
     * @param EvaluableInterface[] $actions
     */
    public function __construct(public ActionTypeEnum $actionType, public array $actions)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateActionsSequence($this);
    }

    /**
     * @inheritDoc
     */
    public function convertToString(array $nodes): string
    {
        return $this->actionType->value . ' ' . implode(', ', $nodes);
    }
}
