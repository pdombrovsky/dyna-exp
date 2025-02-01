<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;

final readonly class KeyCondition implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    /**
     * @var array<EvaluableInterface|string>
     */
    public array $nodes;

    /**
     * @param KeyConditionTypeEnum $type
     * @param mixed ...$nodes
     */
    public function __construct(public KeyConditionTypeEnum $type, mixed ...$nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateKeyCondition($this);
    }

    /**
     * @inheritDoc
     */
    public function convertToString(array $nodes): string
    {
        return sprintf($this->type->value, ...$nodes);
    }
}
