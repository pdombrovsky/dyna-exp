<?php

namespace DynaExp\Interfaces;

use DynaExp\Interfaces\EvaluatorInterface;

interface EvaluatedNodeInterface
{
    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    function evaluate(EvaluatorInterface $evaluator): string;
}
