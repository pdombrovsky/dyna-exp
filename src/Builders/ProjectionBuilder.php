<?php

namespace DynaExp\Builders;

use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\NodeEvaluatorInterface;
use DynaExp\Interfaces\TreeEvaluatorInterface;

class ProjectionBuilder implements TreeEvaluatorInterface
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
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluateTree(NodeEvaluatorInterface $nodeEvaluator): string
    {
        $evaluatedNodes = array_map(fn(EvaluatedNodeInterface $node) => $node->evaluate($nodeEvaluator), $this->names);

        return empty($evaluatedNodes) ? '' : implode(', ', $evaluatedNodes);
    }
}
