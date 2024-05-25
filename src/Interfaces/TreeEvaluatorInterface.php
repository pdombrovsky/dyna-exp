<?php

namespace DynaExp\Interfaces;

interface TreeEvaluatorInterface
{
    function evaluateTree(EvaluatorInterface $evaluator): string;
}
