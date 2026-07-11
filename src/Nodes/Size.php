<?php

namespace DynaExp\Nodes;

use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;
use function sprintf;

final readonly class Size implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    const FMT_STRING = "size (%s)";

    /**
     * @param PathNode $target
     */
    private function __construct(private PathNode $target)
    {
    }

    /**
     * @param PathNode $target
     * @return Size
     */
    public static function of(PathNode $target): self
    {
        return new self($target);
    }

    /**
     * @return PathNode
     */
    public function target(): PathNode
    {
        return $this->target;
    }

    /**
     * @return list<mixed>
     */
    protected function operands(): array
    {
        return [$this->target];
    }

    /**
     * @param array<int|string, string> $convertedNodes
     */
    protected function format(array $convertedNodes): string
    {
        return sprintf(self::FMT_STRING, ...$convertedNodes);
    }
}
