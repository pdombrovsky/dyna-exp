<?php

namespace DynaExp\Evaluation;

use DynaExp\Evaluation\Aliases\Names;
use DynaExp\Evaluation\Aliases\Values;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\PathNode;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Size;
use DynaExp\Nodes\Update;

class Evaluator implements EvaluatorInterface
{
    /**
     * @var Names
     */
    private Names $aliasNames;

    /**
     * @var Values
     */
    private Values $aliasValues;

    public function __construct()
    {
        $this->aliasNames = new Names();
        $this->aliasValues = new Values();
    }

    /**
     * @param PathNode $path
     * @return string
     */
    public function evaluatePath(PathNode $path): string
    {
        $segments = array_map(
            fn(string|int $segment): string|int => is_int($segment) ? $segment : $this->aliasNames->alias($segment),
            $path->segments
        );

        return $path->convertToString($segments);
    }

    /**
     * @param Size $size
     * @return string
     */
    public function evaluateSize(Size $size): string
    {
        $evaluated = $size->nodes[0]->evaluate($this);

        return $size->convertToString([$evaluated]);
    }

    /**
     * @param Condition $conditionNode
     * @return string
     */
    public function evaluateCondition(Condition $conditionNode): string
    {
        $aliases = $this->evaluateNodeOrAliasValue($conditionNode->nodes);
 
        return $conditionNode->convertToString($aliases);
    }

    /**
     * @param mixed[] $nodes
     * @return string[]
     */
    private function evaluateNodeOrAliasValue(array $nodes): array
    {
        return array_map(
            fn (mixed $node) => ($node instanceof EvaluableInterface) ? $node->evaluate($this) : $this->aliasValues->alias($node),
            $nodes
        );
    }

    /**
     * @param KeyCondition $keyConditionNode
     * @return string
     */
    public function evaluateKeyCondition(KeyCondition $keyConditionNode): string
    {
        $aliases = $this->evaluateNodeOrAliasValue($keyConditionNode->nodes);
 
        return $keyConditionNode->convertToString($aliases);
    }

    /**
     * @param Operation $operation
     * @return string
     */
    public function evaluateOperation(Operation $operation): string
    {
        $aliases = $this->evaluateNodeOrAliasValue($operation->nodes);
       
        return $operation->convertToString($aliases);
    }

    /**
     * @param Action $actionNode
     * @return string
     */
    public function evaluateAction(Action $actionNode): string
    {
        $aliases = $this->evaluateNodeOrAliasValue($actionNode->nodes);
 
        return $actionNode->convertToString($aliases);
    }

    /**
     * @param ActionsSequence $sequence
     * @return string
     */
    public function evaluateActionsSequence(ActionsSequence $sequence): string
    {
        $evaluatedNodes = $this->evaluateNodeOrAliasValue($sequence->actions);
 
        return $sequence->convertToString($evaluatedNodes);
    }

    /**
     * @param Update $updateNode
     * @return string
     */
    public function evaluateUpdate(Update $updateNode): string
    {
        $evaluatedActionSequences = $this->evaluateNodeOrAliasValue($updateNode->sequences);

        return $updateNode->convertToString($evaluatedActionSequences);
    }

    /**
     * @param Projection $projectionNode
     * @return string
     */
    public function evaluateProjection(Projection $projectionNode): string
    {
        $evaluatedProjections = array_map(
            fn (mixed $node) => $node->evaluate($this),
            $projectionNode->attributes
        );

        return $projectionNode->convertToString($evaluatedProjections);
    }

    /**
     * @return array<string,string>
     */
    public function getAttributeNameAliases(): array
    {
        return $this->aliasNames->getMap();
    }

    /**
     * @return array<string,mixed>
     */
    public function getAttributeValueAliases(): array
    {
        return $this->aliasValues->getMap();
    }
}
