<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;

final readonly class Action implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

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

    /**
     * @inheritDoc
     */
    public function convertToString(array $convertedNodes): string
    {
        return sprintf($this->type->fmtString(), ...$convertedNodes);
    }
}
