<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Nodes\EvaluableInterface;
use Stringable;

final readonly class Path implements Stringable, EvaluableInterface
{
    /**
     * @param array<string|int> $segments
     */
    public function __construct(public array $segments)
    {
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluatePath($this);
    }

    /**
     * @inheritDoc
     */
    public function __tostring(): string
    {
        $parts = [];
        $lastIndex = -1;
        foreach ($this->segments as $segment) {

            if (is_int($segment)) {

                $parts[$lastIndex] .= "[$segment]";

            } else {

                $parts[++$lastIndex] = $segment;
            }
        }
        
        return implode('.', $parts);
    }
}
