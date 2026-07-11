<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;

interface EvaluableInterface
{
    /**
     * Evaluates this node through the provided evaluator context.
     */
    public function evaluate(EvaluatorInterface $evaluator): string;
}
