<?php

namespace DynaExp\Interfaces;

use Aws\DynamoDb\Marshaler;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\PathNode;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Size;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\Update;

interface EvaluatorInterface
{
    /**
     * @param PathNode $pathNode
     * @return string
     */
    function evaluatePathNode(PathNode $pathNode): string;

    /**
     * @param Size $sizeNode
     * @return string
     */
    function evaluateSize(Size $sizeNode): string;

    /**
     * @param ActionsSequence $sequence
     * @return string
     */
    function evaluateActionsSequence(ActionsSequence $sequence): string;

    /**
     * @param Condition $conditionNode
     * @return string
     */
    function evaluateCondition(Condition $conditionNode): string;

    /**
     * @param KeyCondition $keyConditionNode
     * @return string
     */
    function evaluateKeyCondition(KeyCondition $keyConditionNode): string;

    /**
     * @param Operation $operand
     * @return string
     */
    function evaluateOperand(Operation $operand): string;

    /**
     * @param Action $actionNode
     * @return string
     */
    function evaluateAction(Action $actionNode): string;

    /**
     * @param Projection $projectionNode
     * @return string
     */
    function evaluateProjection(Projection $projectionNode): string;

    /**
     * @param Update $updateNode
     * @return string
     */
    function evaluateUpdate(Update $updateNode): string;

    /**
     * @return array
     */
    function getExpressionAttributeNames(): array;

    /**
     * @return array
     */
    function getExpressionAttributeValues(Marshaler $marshaler): array;
}
