<?php

namespace DynaExp\Context;

use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Exceptions\UnexpectedValueException;
use DynaExp\Nodes\EvaluableInterface;

final class ExpressionContext
{
    /**
     * @param array<string, EvaluableInterface|mixed> $components
     */
    public function __construct(private array $components)
    {
    }

    /**
     * @param ExpressionTypeEnum $type
     * @return bool
     */
    public function has(ExpressionTypeEnum $type): bool
    {
        return isset($this->components[$type->value]);
    }

    /**
     * @return array<string,string|mixed>
     */
    public function toArray(?callable $valuesTransformator = null): array
    {
        $components = $this->components;

        if (is_callable($valuesTransformator) && $this->has(ExpressionTypeEnum::values)) {

            $transformedValues = $valuesTransformator($this->components[ExpressionTypeEnum::values->value]);

            if (!is_array($transformedValues)) {

                throw new UnexpectedValueException("Callback returned an invalid result type: expected 'array', got: " . gettype($transformedValues));
            }

            $components[ExpressionTypeEnum::values->value] = $transformedValues;
        }

        return $components;
    }
}
