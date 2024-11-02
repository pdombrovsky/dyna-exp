<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;

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
}
