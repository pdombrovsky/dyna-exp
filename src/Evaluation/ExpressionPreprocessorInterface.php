<?php

namespace DynaExp\Evaluation;

use DynaExp\Nodes\EvaluableInterface;

interface ExpressionPreprocessorInterface
{
    /**
     * Advanced hook for rewriting only the current expression node before evaluation.
     *
     * Implementations are expected to:
     * - operate on the current node only
     * - return the original node when no rewrite is needed
     * - be idempotent
     *
     * Child nodes are preprocessed by Evaluator during recursive evaluation.
     */
    public function process(EvaluableInterface $node): EvaluableInterface;
}
