<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Evaluation\EvaluatorInterface;

final readonly class KeyCondition implements EvaluableInterface
{
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
}
