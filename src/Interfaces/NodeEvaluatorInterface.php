<?php

namespace DynaExp\Interfaces;

use DynaExp\Nodes\Condition;
use DynaExp\Nodes\Dot;
use DynaExp\Nodes\Index;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Name;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Size;
use DynaExp\Nodes\Update;

interface NodeEvaluatorInterface
{
    function evaluateName(Name $nameNode): string;
    function evaluateIndex(Index $indexNode): string;
    function evaluateDot(Dot $dotNode): string;
    function evaluateSize(Size $sizeNode): string;
    function evaluateCondition(Condition $conditionNode): string;
    function evaluateKeyCondition(KeyCondition $keyConditionNode): string;
    function evaluateOperation(Operation $operationNode): string;
    function evaluateUpdate(Update $updateNode): string;
    function getNames(): array;
    function getValues(): array;
}
