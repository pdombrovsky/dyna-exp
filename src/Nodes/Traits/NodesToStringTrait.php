<?php

namespace DynaExp\Nodes\Traits;

use Stringable;

trait NodesToStringTrait 
{
    /**
     * @return string[]
     */
    private function nodesToString(): array
    {
        return array_map(
            fn (mixed $node) => static::convert($node),
            $this->nodes
        );
    }

    /**
     * @param mixed $value
     * @return string
     */
    private static function convert(mixed $value): string
    {
        return match (true) {
            $value instanceof Stringable => $value->__toString(),
            is_object($value) && method_exists($value, 'toArray') => print_r($value->toArray(), true),

            default => print_r($value, true)
        };
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->convertToString($this->nodesToString());
    }
}
