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
        return $this->convertToString(fn (string $segment) => $segment);
    }

    /**
     * @param callable(string $segment): string $stringSegmentTransformer
     * @return string
     */
    public function convertToString(callable $stringSegmentTransformer): string
    {
        $parts = [];
        $lastIndex = -1;
        foreach ($this->segments as $segment) {

            if (is_int($segment)) {

                $parts[$lastIndex] .= "[$segment]";

            } else {

                $parts[++$lastIndex] = $stringSegmentTransformer($segment);
            }
        }
        
        return implode('.', $parts);
    }
}
