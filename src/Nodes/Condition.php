<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Enums\ConditionTypeEnum;

final readonly class Condition implements EvaluableInterface
{
    /**
     * @var array<EvaluableInterface|mixed>
     */
    public array $nodes; 

    /**
     * @param ConditionTypeEnum $type
     * @param mixed ...$nodes
     */
    public function __construct(public ConditionTypeEnum $type, mixed ...$nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateCondition($this);
    }
}
