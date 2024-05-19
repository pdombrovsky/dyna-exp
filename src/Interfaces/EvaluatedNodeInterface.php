<?php

namespace DynaExp\Interfaces;

use DynaExp\Interfaces\NodeEvaluatorInterface;

interface EvaluatedNodeInterface
{
    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    function evaluate(NodeEvaluatorInterface $evaluator): string;
}
