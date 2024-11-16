<?php

namespace DynaExp\Evaluation;

use DynaExp\Nodes\Path;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Size;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\Update;

interface EvaluatorInterface
{
    /**
     * @param Path $path
     * @return string
     */
    function evaluatePath(Path $path): string;

    /**
     * @param Size $size
     * @return string
     */
    function evaluateSize(Size $size): string;

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
     * @param Operation $operation
     * @return string
     */
    function evaluateOperation(Operation $operation): string;

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
     * @return array<string,string>
     */
    function getAttributeNameAliases(): array;

    /**
     * @return array<string,mixed>
     */
    function getAttributeValueAliases(): array;
}
