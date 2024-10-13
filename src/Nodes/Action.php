<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;

final readonly class Action implements EvaluableInterface
{
    /**
     * @param EvaluableInterface $left
     * @param ActionTypeEnum $type
     * @param mixed $right
     */
    public function __construct(public EvaluableInterface $left, public ActionTypeEnum $type, public mixed $right = null)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateAction($this);
    }
}
