<?php

namespace DynaExp;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\KeyConditionBuilder;
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Evaluation\Evaluator;
use DynaExp\Interfaces\EvaluatorInterface;
use DynaExp\Interfaces\TreeEvaluatorInterface;
use RuntimeException;

final class ExpressionBuilder
{
    /**
     * @var array<ExpressionTypeEnum, TreeEvaluatorInterface>
     */
    private array $expressionMap;

    public function __construct()
    {
        $this->expressionMap = [];
    }

    /**
     * @param ConditionBuilder $conditionBuilder
     * @return ExpressionBuilder
     */
    public function setConditionBuilder(ConditionBuilder $conditionBuilder): ExpressionBuilder
    {
        $this->expressionMap[ExpressionTypeEnum::condition->name] = $conditionBuilder;

        return $this;
    }

    /**
     * @param ConditionBuilder $conditionBuilder
     * @return ExpressionBuilder
     */
    public function setFilterBuilder(ConditionBuilder $conditionBuilder): ExpressionBuilder
    {
        $this->expressionMap[ExpressionTypeEnum::filter->name] = $conditionBuilder;

        return $this;
    }

    /**
     * @param ProjectionBuilder $projectionBuilder
     * @return ExpressionBuilder
     */
    public function setProjectionBuilder(ProjectionBuilder $projectionBuilder): ExpressionBuilder
    {
        $this->expressionMap[ExpressionTypeEnum::projection->name] = $projectionBuilder;

        return $this;
    }

    /**
     * @param KeyConditionBuilder $keyConditionBuilder
     * @return ExpressionBuilder
     */
    public function setKeyConditionBuilder(KeyConditionBuilder $keyConditionBuilder): ExpressionBuilder
    {
        $this->expressionMap[ExpressionTypeEnum::key->name] = $keyConditionBuilder;

        return $this;
    }

    /**
     * @param UpdateBuilder $updateBuilder
     * @return ExpressionBuilder
     */
    public function setUpdateBuilder(UpdateBuilder $updateBuilder): ExpressionBuilder
    {
        $this->expressionMap[ExpressionTypeEnum::update->name] = $updateBuilder;

        return $this;
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return Expression
     */
    public function build(EvaluatorInterface $evaluator = new Evaluator()): Expression
    {
        if (empty($this->expressionMap)) {
            throw new RuntimeException('There are no expressions to build');
        }

        $expressionMap = $this->buildChildTrees($evaluator);

        return new Expression($expressionMap, $evaluator->getNames(), $evaluator->getValues());
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return array
     */
    private function buildChildTrees(EvaluatorInterface $evaluator): array
    {
        $formattedExpressions = [];

        foreach (ExpressionTypeEnum::cases() as $expressionType) {

            if (empty($this->expressionMap[$expressionType->name])) {

                continue;
            }

            if ($formattedExpression = $this->expressionMap[$expressionType->name]->evaluateTree($evaluator)) {

                $formattedExpressions[$expressionType->name] = $formattedExpression;
            }
        }

        return $formattedExpressions;
    }
}
