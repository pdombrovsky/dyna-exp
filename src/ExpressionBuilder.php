<?php

namespace DynaExp;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\KeyConditionBuilder;
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Evaluation\NodeEvaluator;
use DynaExp\Interfaces\NodeEvaluatorInterface;
use DynaExp\Interfaces\TreeEvaluatorInterface;


class ExpressionBuilder
{
    /**
     * @var array
     */
    private array $expressionMap;

    /**
     * @param array<ExpressionTypeEnum, TreeEvaluatorInterface> $expressionMap
     */
    public function __construct()
    {
        $this->expressionMap = [];
    }

    /**
     * @param ConditionBuilder $conditionBuilder
     * @return ExpressionBuilder
     */
    public function setConditionBuilder(ConditionBuilder $conditionBuilder): static
    {
        $this->expressionMap[ExpressionTypeEnum::condition->name] = $conditionBuilder;

        return $this;
    }

    /**
     * @param ConditionBuilder $conditionBuilder
     * @return ExpressionBuilder
     */
    public function setFilterBuilder(ConditionBuilder $conditionBuilder): static
    {
        $this->expressionMap[ExpressionTypeEnum::filter->name] = $conditionBuilder;

        return $this;
    }

    /**
     * @param ProjectionBuilder $projectionBuilder
     * @return ExpressionBuilder
     */
    public function setProjectionBuilder(ProjectionBuilder $projectionBuilder): static
    {
        $this->expressionMap[ExpressionTypeEnum::projection->name] = $projectionBuilder;

        return $this;
    }

    /**
     * @param KeyConditionBuilder $keyConditionBuilder
     * @return ExpressionBuilder
     */
    public function setKeyConditionBuilder(KeyConditionBuilder $keyConditionBuilder): static
    {
        $this->expressionMap[ExpressionTypeEnum::key->name] = $keyConditionBuilder;

        return $this;
    }

    /**
     * @param UpdateBuilder $updateBuilder
     * @return ExpressionBuilder
     */
    public function setUpdateBuilder(UpdateBuilder $updateBuilder): static
    {
        $this->expressionMap[ExpressionTypeEnum::update->name] = $updateBuilder;

        return $this;
    }

    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return Expression
     */
    public function build(NodeEvaluatorInterface $evaluator = new NodeEvaluator()): Expression
    {
        $expressionMap = $this->buildChildTrees($evaluator);

        return new Expression($expressionMap, $evaluator->getNames(), $evaluator->getValues());
    }

    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return array
     */
    private function buildChildTrees(NodeEvaluatorInterface $evaluator): array
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
