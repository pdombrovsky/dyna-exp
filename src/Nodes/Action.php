<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Exceptions\RuntimeException;
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
        $fmtString = match ($this->type) {
            ActionTypeEnum::set => '%s = %s',
            ActionTypeEnum::add,
            ActionTypeEnum::delete => '%s %s',
            ActionTypeEnum::remove => '%s',

            default => throw new RuntimeException("Action is unknown"),
        };

        return sprintf($fmtString, ...$convertedNodes);
    }
}
