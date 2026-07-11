<?php

namespace DynaExp\Builders;

use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Evaluation\Evaluator;
use DynaExp\Evaluation\ExpressionPreprocessorInterface;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Update;
use DynaExp\Result\ExpressionResult;

final class ExpressionBuilder
{
    /**
     * @var array<string, null|EvaluableInterface>
     */
    private array $nodes;

    /**
     * @param ?ExpressionPreprocessorInterface $preprocessor Optional advanced hook for local node rewrites before evaluation.
     */
    public function __construct(private readonly ?ExpressionPreprocessorInterface $preprocessor = null)
    {
        $this->nodes = [];
    }

    /**
     * @param ?Condition $condition
     * @return ExpressionBuilder
     */
    public function setCondition(?Condition $condition): ExpressionBuilder
    {
        $this->nodes[ExpressionTypeEnum::condition->name] = $condition;

        return $this;
    }

    /**
     * @param ?Condition $condition
     * @return ExpressionBuilder
     */
    public function setFilter(?Condition $condition): ExpressionBuilder
    {
        $this->nodes[ExpressionTypeEnum::filter->name] = $condition;

        return $this;
    }

    /**
     * @param ?Projection $projection
     * @return ExpressionBuilder
     */
    public function setProjection(?Projection $projection): ExpressionBuilder
    {
        $this->nodes[ExpressionTypeEnum::projection->name] = $projection;

        return $this;
    }

    /**
     * @param ?KeyCondition $keyCondition
     * @return ExpressionBuilder
     */
    public function setKeyCondition(?KeyCondition $keyCondition): ExpressionBuilder
    {
        $this->nodes[ExpressionTypeEnum::keyCondition->name] = $keyCondition;

        return $this;
    }

    /**
     * @param ?Update $update
     * @return ExpressionBuilder
     */
    public function setUpdate(?Update $update): ExpressionBuilder
    {
        $this->nodes[ExpressionTypeEnum::update->name] = $update;

        return $this;
    }

    /**
     * Builds final expression result with rendered strings and alias maps.
     * A fresh evaluator instance is created for every build invocation.
     *
     * @return ExpressionResult
     */
    public function build(): ExpressionResult
    {
        $evaluator = new Evaluator($this->preprocessor);

        $components = [];

        foreach (ExpressionTypeEnum::cases() as $expressionType) {

            $node = $this->nodes[$expressionType->name] ?? null;

            $evaluated = $node !== null ? $evaluator->evaluate($node) : null;

            if ($evaluated) {

                $components[$expressionType->value] = $evaluated;
            }
        }

        if ($expressionAttributeNames = $evaluator->getAttributeNameAliases()) {

            $components[ExpressionTypeEnum::names->value] = $expressionAttributeNames;
        }

        if ($expressionAttributeValues = $evaluator->getAttributeValueAliases()) {

            $components[ExpressionTypeEnum::values->value] = $expressionAttributeValues;
        }
     
        return new ExpressionResult($components);
    }
}
