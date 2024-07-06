<?php

namespace DynaExp\Builders;

use DynaExp\Builders\Name;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;
use DynaExp\Interfaces\TreeEvaluatorInterface;

final class ProjectionBuilder implements TreeEvaluatorInterface
{
    /**
     * @var array
     */
    private array $names;

    /**
     * @param Name ...$names
     */
    public function __construct(Name ...$names)
    {
        $this->names = [];

        foreach ($names as $nameBulder) {

            $this->names[] = $nameBulder->getCurrentNode();
        }
    }

    /**
     * @param Name ...$names
     * @return ProjectionBuilder
     */
    public function add(Name ...$names) : ProjectionBuilder
    {
        foreach ($names as $nameBuilder) {

            $this->names[] = $nameBuilder->getCurrentNode();
        }

        return $this;
    }

    /**
     * @return EvaluatedNodeInterface[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluateTree(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateProjection($this);
    }
}
