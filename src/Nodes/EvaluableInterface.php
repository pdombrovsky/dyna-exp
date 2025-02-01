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
     * @param array<EvaluableInterface|mixed> $nodes
     * @return string
     */
    function convertToString(array $nodes): string;
}
