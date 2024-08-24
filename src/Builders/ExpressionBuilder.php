<?php

namespace DynaExp\Builders;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\KeyConditionBuilder;
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Context\ExpressionContext;
use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Interfaces\BuilderInterface;

final class ExpressionBuilder
{
    /**
     * @var array<string, BuilderInterface>
     */
    private array $builders;

    public function __construct()
    {
        $this->builders = [];
    }

    /**
     * @param ConditionBuilder $conditionBuilder
     * @return ExpressionBuilder
     */
    public function setConditionBuilder(ConditionBuilder $conditionBuilder): ExpressionBuilder
    {
        $this->builders[ExpressionTypeEnum::condition->value] = $conditionBuilder;

        return $this;
    }

    /**
     * @param ConditionBuilder $conditionBuilder
     * @return ExpressionBuilder
     */
    public function setFilterBuilder(ConditionBuilder $conditionBuilder): ExpressionBuilder
    {
        $this->builders[ExpressionTypeEnum::filter->value] = $conditionBuilder;

        return $this;
    }

    /**
     * @param ProjectionBuilder $projectionBuilder
     * @return ExpressionBuilder
     */
    public function setProjectionBuilder(ProjectionBuilder $projectionBuilder): ExpressionBuilder
    {
        $this->builders[ExpressionTypeEnum::projection->value] = $projectionBuilder;

        return $this;
    }

    /**
     * @param KeyConditionBuilder $keyConditionBuilder
     * @return ExpressionBuilder
     */
    public function setKeyConditionBuilder(KeyConditionBuilder $keyConditionBuilder): ExpressionBuilder
    {
        $this->builders[ExpressionTypeEnum::keyCondition->value] = $keyConditionBuilder;

        return $this;
    }

    /**
     * @param UpdateBuilder $updateBuilder
     * @return ExpressionBuilder
     */
    public function setUpdateBuilder(UpdateBuilder $updateBuilder): ExpressionBuilder
    {
        $this->builders[ExpressionTypeEnum::update->value] = $updateBuilder;

        return $this;
    }

    /**
     * @return ExpressionContext
     */
    public function build(): ExpressionContext
    {
        $components = [];

        foreach ($this->builders as $type => $builder) {

            if ($evaluableExpression = $builder->build()) {

                $components[$type] = $evaluableExpression;
            }
        }

        return new ExpressionContext($components);
    }
}
