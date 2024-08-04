<?php

namespace DynaExp\Context;

use Aws\DynamoDb\Marshaler;
use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Interfaces\EvaluableInterface;
use DynaExp\Interfaces\EvaluatorInterface;

final class ExpressionContext
{
    /**
     * @param array<string, EvaluableInterface> $components
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
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function expressions(EvaluatorInterface $evaluator, Marshaler $marshaler): array
    {
        $evaluated = array_map(
            fn(EvaluableInterface $component) => $component->evaluate($evaluator),
            $this->components
        );

        $evaluated += $evaluator->getExpressionAttributeNames();
        $evaluated += $evaluator->getExpressionAttributeValues($marshaler);

        return $evaluated;
    }
}
