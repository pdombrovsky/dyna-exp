<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;

final readonly class Size implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    const FMT_STRING = "size (%s)";

    /**
     * @param array<EvaluableInterface> $nodes
     */
    public function __construct(public array $nodes)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateSize($this);
    }

    /**
     * @inheritDoc
     */
    public function convertToString(array $convertedNodes): string
    {
        return sprintf(self::FMT_STRING, ...$convertedNodes);
    }
}
