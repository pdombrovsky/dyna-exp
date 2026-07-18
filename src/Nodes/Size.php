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
     * @param Path $target
     */
    private function __construct(private Path $target)
    {
    }

    /**
     * @param Path $target
     * @return Size
     */
    public static function of(Path $target): self
    {
        return new self($target);
    }

    /**
     * @return Path
     */
    public function target(): Path
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
