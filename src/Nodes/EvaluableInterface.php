<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;

interface EvaluableInterface
{
    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    function evaluate(EvaluatorInterface $evaluator): string;
}
