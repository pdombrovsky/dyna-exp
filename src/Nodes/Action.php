<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;

final readonly class Action implements EvaluableInterface
{
    /**
     * @var mixed[]
     */
    public array $nodes;

    /**
     * @param ActionTypeEnum $type
     * @param mixed ...$nodes
     */
    public function __construct(public ActionTypeEnum $type, mixed ...$nodes)
    {
        $this->nodes = $nodes;
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
