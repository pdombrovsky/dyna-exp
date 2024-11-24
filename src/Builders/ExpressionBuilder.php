<?php

namespace DynaExp\Builders;

use DynaExp\Context\ExpressionContext;
use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Evaluation\EvaluatorFactory;
use DynaExp\Evaluation\EvaluatorFactoryInterface;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Update;

final class ExpressionBuilder
{
    /**
     * @var array<string, null|EvaluableInterface>
     */
    private array $evaluables;

    public function __construct()
    {
        $this->evaluables = [];
    }

    /**
     * @param ?Condition $condition
     * @return ExpressionBuilder
     */
    public function setCondition(?Condition $condition): ExpressionBuilder
    {
        $this->evaluables[ExpressionTypeEnum::condition->name] = $condition;

        return $this;
    }

    /**
     * @param ?Condition $condition
     * @return ExpressionBuilder
     */
    public function setFilter(?Condition $condition): ExpressionBuilder
    {
        $this->evaluables[ExpressionTypeEnum::filter->name] = $condition;

        return $this;
    }

    /**
     * @param ?Projection $projection
     * @return ExpressionBuilder
     */
    public function setProjection(?Projection $projection): ExpressionBuilder
    {
        $this->evaluables[ExpressionTypeEnum::projection->name] = $projection;

        return $this;
    }

    /**
     * @param ?KeyCondition $keyCondition
     * @return ExpressionBuilder
     */
    public function setKeyCondition(?KeyCondition $keyCondition): ExpressionBuilder
    {
        $this->evaluables[ExpressionTypeEnum::keyCondition->name] = $keyCondition;

        return $this;
    }

    /**
     * @param ?Update $update
     * @return ExpressionBuilder
     */
    public function setUpdate(?Update $update): ExpressionBuilder
    {
        $this->evaluables[ExpressionTypeEnum::update->name] = $update;

        return $this;
    }

    /**
     * @param EvaluatorFactoryInterface $factory
     * @return ExpressionContext
     */
    public function build(EvaluatorFactoryInterface $factory = new EvaluatorFactory()): ExpressionContext
    {
        $evaluator = $factory->make();

        $components = [];

        foreach (ExpressionTypeEnum::cases() as $expressionType) {

            $evaluable = $this->evaluables[$expressionType->name] ?? null;

            $evaluated = $evaluable?->evaluate($evaluator);

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
     
        return new ExpressionContext($components);
    }
}
