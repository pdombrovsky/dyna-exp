<?php

namespace DynaExp\Interfaces;

interface TreeEvaluatorInterface
{
    function evaluateTree(NodeEvaluatorInterface $evaluator): string;
}
