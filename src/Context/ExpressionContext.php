<?php

namespace DynaExp\Context;

use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Exceptions\UnexpectedValueException;

final class ExpressionContext
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
     * @param null|callable(array<string,mixed>):array<string,mixed> $valuesTransform Callback to transform ExpressionAttributeValues map
     * @return array<string, string|array<string, mixed>>
     */
    public function toArray(?callable $valuesTransform = null): array
    {
        $components = $this->components;

        if (is_callable($valuesTransform) && $this->has(ExpressionTypeEnum::values)) {

            /** @var array<string, mixed> $values */
            $values = $this->components[ExpressionTypeEnum::values->value];

            $transformedValues = $valuesTransform($values);

            if (!is_array($transformedValues)) {

                throw new UnexpectedValueException("Callback returned an invalid result type: expected 'array', got: " . gettype($transformedValues));
            }

            $components[ExpressionTypeEnum::values->value] = $transformedValues;
        }

        return $components;
    }
}
