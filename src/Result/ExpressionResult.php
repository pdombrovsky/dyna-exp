<?php

namespace DynaExp\Result;

use DynaExp\Enums\ExpressionTypeEnum;

final class ExpressionResult
{
    /**
     * @param array<string, string|array<string, mixed>> $components
     */
    public function __construct(private array $components)
    {
    }

    /**
     * Checks existence of a component by type.
     */
    public function has(ExpressionTypeEnum $type): bool
    {
        return isset($this->components[$type->value]);
    }

    /**
     * Exports stored expression components as-is.
     *
     * @return array<string, string|array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->components;
    }
}
