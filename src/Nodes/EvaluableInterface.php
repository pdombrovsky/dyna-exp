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

    /**
     * @param array<string|int> $convertedNodes
     * @return string
     */
    function convertToString(array $convertedNodes): string;
}
