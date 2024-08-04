<?php

namespace DynaExp\Interfaces;

use DynaExp\Interfaces\EvaluatorInterface;

interface EvaluableInterface
{
    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    function evaluate(EvaluatorInterface $evaluator): string;
}
