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
    public function __toString(): string
    {
        return $this->convertToString($this->segments);
    }

    /**
     * @inheritDoc
     */
    public function convertToString(array $convertedNodes): string
    {
        $parts = [];
        $lastIndex = -1;
        foreach ($convertedNodes as $segment) {

            if (is_int($segment)) {

                $parts[$lastIndex] .= "[$segment]";

            } else {

                $parts[++$lastIndex] = $segment;
            }
        }
        
        return implode('.', $parts);
    }

    /**
     * Returns parent path node segments if exists
     * 
     * @return array<string|int>
     */
    public function parentPathSegments(): array
    {
        return array_slice($this->segments, 0, -1);
    }

    /**
     * Returns last segment for given path
     * 
     * @return int|string
     */
    public function lastSegment(): int|string
    {
        return $this->segments[array_key_last($this->segments)];
    }
}
