<?php

namespace DynaExp\Nodes;

use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;
use function get_debug_type;
use function implode;
use function sprintf;

final readonly class Projection implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    /**
     * @param array<int|string, Path> $attributes
     */
    public function __construct(public array $attributes)
    {
        if ($attributes === []) {
            throw new InvalidArgumentException('Projection requires at least one attribute.');
        }

        foreach ($attributes as $attribute) {
            if (! $attribute instanceof Path) {
                throw new InvalidArgumentException(sprintf(
                    'Projection attribute must be %s, %s given.',
                    Path::class,
                    get_debug_type($attribute),
                ));
            }
        }
    }

    /**
     * @return array<int|string, Path>
     */
    protected function operands(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<int|string, string> $convertedNodes
     */
    protected function format(array $convertedNodes): string
    {
        return implode(', ', $convertedNodes);
    }
}
