<?php

namespace DynaExp\Builders;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\NodeEvaluatorInterface;
use DynaExp\Interfaces\TreeEvaluatorInterface;
use DynaExp\Nodes\KeyCondition;

use RuntimeException;

class KeyConditionBuilder implements TreeEvaluatorInterface
{
    /**
     * @var EvaluatedNodeInterface
     */
    private EvaluatedNodeInterface $current;

    /**
     * @param KeyCondition $primaryKeyCondition
     * @throws RuntimeException
     */
    public function __construct(KeyCondition $primaryKeyCondition)
    {
        if ($primaryKeyCondition->type !== KeyConditionTypeEnum::equalKeyCond) {

            throw new RuntimeException("Equal key condition is allowed for primary key only");

        }

        $this->current = $primaryKeyCondition;
    }

    /**
     * @param KeyCondition $sortKeyCondition
     * @return void
     */
    public function and(KeyCondition $sortKeyCondition)
    {
        $this->current = new KeyCondition($this->current, KeyConditionTypeEnum::andKeyCond, right: $sortKeyCondition);
    }

    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluateTree(NodeEvaluatorInterface $nodeEvaluator): string
    {
        return $this->current->evaluate($nodeEvaluator);
    }
}
