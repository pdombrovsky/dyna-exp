<?php

namespace DynaExp\Evaluation;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Evaluation\Aliases\Names;
use DynaExp\Evaluation\Aliases\Values;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\PathNode;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Size;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\Update;

use RuntimeException;

final class Evaluator implements EvaluatorInterface
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
     * @param PathNode $dotNode
     * @return string
     */
    public function evaluatePathNode(PathNode $pathNode): string
    {
        $left = $pathNode->left instanceof EvaluableInterface ?
            $pathNode->left->evaluate($this) :
            $this->aliasNames->alias($pathNode->left);

        if (null === $pathNode->right) {
            return $left;
        }

        $right = $pathNode->right instanceof EvaluableInterface ?
            '.' . $pathNode->right->evaluate($this) :
            "[$pathNode->right]";

        return "$left$right";
    }

    /**
     * @param Size $sizeNode
     * @return string
     */
    public function evaluateSize(Size $sizeNode): string
    {
        return "size ({$sizeNode->node->evaluate($this)})";
    }

    /**
     * @param Condition $conditionNode
     * @return string
     */
    public function evaluateCondition(Condition $conditionNode): string
    {
        $fmtString = match ($conditionNode->type) {

            ConditionTypeEnum::equalCond => '%s = %s',
            ConditionTypeEnum::notEqualCond => '%s <> %s',
            ConditionTypeEnum::lessThanCond => '%s < %s',
            ConditionTypeEnum::lessThanEqualCond => '%s <= %s',
            ConditionTypeEnum::greaterThanCond => '%s > %s',
            ConditionTypeEnum::greaterThanEqualCond => '%s >= %s',
            ConditionTypeEnum::attrTypeCond => 'attribute_type (%s, %s)',
            ConditionTypeEnum::beginsWithCond => 'begins_with (%s, %s)',
            ConditionTypeEnum::containsCond => 'contains (%s, %s)',
            ConditionTypeEnum::betweenCond => '%s BETWEEN %s AND %s',
            ConditionTypeEnum::attrExistsCond => 'attribute_exists (%s)',
            ConditionTypeEnum::attrNotExistsCond => 'attribute_not_exists (%s)',
            ConditionTypeEnum::inCond => '%s IN (' . str_repeat('%s, ', count($conditionNode->nodes) - 2) . '%s)',
            ConditionTypeEnum::notCond => 'NOT %s',
            ConditionTypeEnum::andCond => '%s AND %s',
            ConditionTypeEnum::orCond => '%s OR %s',
            ConditionTypeEnum::parenthesesCond => '(%s)',

            default => throw new RuntimeException("Condition type is unknown"),
        };

        $aliases = $this->evaluateNodes($conditionNode->nodes);
 
        return sprintf($fmtString, ...$aliases);
    }

    /**
     * @param mixed[] $nodes
     * @return string[]
     */
    private function evaluateNodes(array $nodes): array
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
        $fmtString = match ($keyConditionNode->type) {

            KeyConditionTypeEnum::equalKeyCond => '%s = %s',
            KeyConditionTypeEnum::lessThanKeyCond => '%s < %s',
            KeyConditionTypeEnum::lessThanEqualKeyCond => '%s <= %s',
            KeyConditionTypeEnum::greaterThanKeyCond => '%s > %s',
            KeyConditionTypeEnum::greaterThanEqualKeyCond => '%s >= %s',
            KeyConditionTypeEnum::beginsWithKeyCond => 'begins_with (%s, %s)',
            KeyConditionTypeEnum::betweenKeyCond => '%s BETWEEN %s AND %s',
            KeyConditionTypeEnum::andKeyCond => '%s AND %s',

            default => throw new RuntimeException("KeyCondition type is unknown"),
        };

        $aliases = $this->evaluateNodes($keyConditionNode->nodes);
 
        return sprintf($fmtString, ...$aliases);
    }

    /**
     * @param Operation $operation
     * @throws RuntimeException
     * @return string
     */
    public function evaluateOperation(Operation $operation): string
    {
        $fmtString = match ($operation->type) {

            OperationTypeEnum::plusValue => '%s + %s',
            OperationTypeEnum::minusValue => '%s - %s',
            OperationTypeEnum::ifNotExists => 'if_not_exists(%s, %s)',
            OperationTypeEnum::listAppend,
            OperationTypeEnum::listPrepend => 'list_append(%s, %s)',

            default => throw new RuntimeException("Operation type is unknown"),
        };

        $aliases = $this->evaluateNodes($operation->nodes);

        if (OperationTypeEnum::listPrepend == $operation->type) {

            $aliases = array_reverse($aliases);

        }

        return sprintf($fmtString, ...$aliases);
    }

    /**
     * @param Action $operationNode
     * @throws RuntimeException
     * @return string
     */
    public function evaluateAction(Action $actionNode): string
    {
        $fmtString = match ($actionNode->type) {

            ActionTypeEnum::set => '%s = %s',
            ActionTypeEnum::add,
            ActionTypeEnum::delete => '%s %s',
            ActionTypeEnum::remove => '%s',

            default => throw new RuntimeException("Action is unknown"),
        };

        $aliases = $this->evaluateNodes($actionNode->nodes);
 
        return sprintf($fmtString, ...$aliases);
    }

    /**
     * @param ActionsSequence $sequence
     * @return string
     */
    public function evaluateActionsSequence(ActionsSequence $sequence): string
    {
        $type = match ($sequence->actionType) {

            ActionTypeEnum::set => 'SET',
            ActionTypeEnum::add => 'ADD',
            ActionTypeEnum::remove => 'REMOVE',
            ActionTypeEnum::delete => 'DELETE',

            default => throw new RuntimeException("Update operation type is unknown"),
        };

        $evaluatedNodes = $this->evaluateNodes($sequence->actions);
 
        return $type . ' ' . implode(', ', $evaluatedNodes);
    }

    /**
     * @param Update $updateNode
     * @return string
     */
    public function evaluateUpdate(Update $updateNode): string
    {
        $evaluatedActionSequences = $this->evaluateNodes($updateNode->sequences);

        return implode(' ', $evaluatedActionSequences);
    }

    /**
     * @param Projection $projectionNode
     * @return string
     */
    public function evaluateProjection(Projection $projectionNode): string
    {
        $evaluatedProjections = $this->evaluateNodes($projectionNode->attributes);

        return implode(', ', $evaluatedProjections);
    }

    /**
     * @return array
     */
    public function getAttributeNameAliases(): array
    {
        return $this->aliasNames->getMap();
    }

    /**
     * @return array
     */
    public function getAttributeValueAliases(): array
    {
        return $this->aliasValues->getMap();
    }
}
