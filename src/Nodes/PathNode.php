<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Nodes\EvaluableInterface;
use Stringable;

final readonly class PathNode implements Stringable, EvaluableInterface
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
     * Returns JMESPath search expression
     * 
     * @param bool $resetIndexes
     * @return string
     */
    public function searchExpression(bool $resetIndexes = false): string
    {
        $segments = array_map(
            fn(string|int $segment) => is_int($segment) ? ($resetIndexes ? 0 : $segment): "\"$segment\"",
            $this->segments
        );

        return $this->convertToString($segments);
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
     * Returns parent path node if exists
     * 
     * @return PathNode
     */
    public function parent(): ?self
    {
        $parentSegments = array_slice($this->segments, 0, -1);

        return $parentSegments ? new self($parentSegments) : null;
    }

    /**
     * Returns child path node for given segments
     * 
     * @param array<string|int> $segments
     * @return PathNode
     */
    public function child(array $segments): self
    {
        return new self([...$this->segments, ...$segments]);
    }

    /**
     * Check if the current node is parent of other
     * 
     * @param PathNode $other
     * @return bool
     */
    public function isParentOf(PathNode $other): bool
    {
        $p = $this->segments;
        $c = $other->segments;

        if (count($p) >= count($c)) {

            return false;
        }

        return array_slice($c, 0, count($p)) === $p;
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
