<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;

final readonly class Condition implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

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

    /**
     * @inheritDoc
     */
    public function convertToString(array $nodes): string
    {
        if ($this->type === ConditionTypeEnum::inCond) {

            $fmtString = str_replace('(%s)', '(' . str_repeat('%s, ', count($nodes) - 2) . '%s)', $this->type->value);
        }
        else {

            $fmtString = $this->type->value;
        }
 
        return sprintf($fmtString, ...$nodes);
    }
}
