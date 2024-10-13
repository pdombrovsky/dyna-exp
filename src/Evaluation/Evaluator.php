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
            ConditionTypeEnum::inCond => '%s IN (%s)',
            ConditionTypeEnum::notCond => 'NOT %s',
            ConditionTypeEnum::andCond => '%s AND %s',
            ConditionTypeEnum::orCond => '%s OR %s',
            ConditionTypeEnum::parenthesesCond => '(%s)',

            default => throw new RuntimeException("Condition type is unknown"),
        };

        $firstAlias = $conditionNode->node->evaluate($this);

        $secondAlias = match ($conditionNode->type) {

            ConditionTypeEnum::attrExistsCond,
            ConditionTypeEnum::attrNotExistsCond,
            ConditionTypeEnum::parenthesesCond,
            ConditionTypeEnum::notCond => [],
            ConditionTypeEnum::andCond,
            ConditionTypeEnum::orCond => [$conditionNode->right->evaluate($this)],

            default => array_map(fn(mixed $value) => $this->aliasValues->alias($value), $conditionNode->right),
        };

        $aliases = ConditionTypeEnum::inCond !== $conditionNode->type ?
            [$firstAlias, ...$secondAlias] :
            [$firstAlias, implode(', ', $secondAlias)];

        return sprintf($fmtString, ...$aliases);
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

        $firstAlias = $keyConditionNode->node->evaluate($this);
        $secondAlias = KeyConditionTypeEnum::andKeyCond === $keyConditionNode->type ?
            [$keyConditionNode->right->evaluate($this)] :
            array_map(fn(mixed $value) => $this->aliasValues->alias($value), $keyConditionNode->right);


        return sprintf($fmtString, $firstAlias, ...$secondAlias);
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

        $leftAlias = $operation->node->evaluate($this);
        $rightAlias = $operation->value instanceof EvaluableInterface ?
            $operation->value->evaluate($this) :
            $this->aliasValues->alias($operation->value);

        $aliases = (OperationTypeEnum::listPrepend == $operation->type) ?
            [$rightAlias, $leftAlias] :
            [$leftAlias, $rightAlias];

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

        $leftAlias = $actionNode->left->evaluate($this);

        if (ActionTypeEnum::remove === $actionNode->type) {
            $aliases = [$leftAlias];
        } else {

            $rightAlias = $actionNode->right instanceof EvaluableInterface ?
                $actionNode->right->evaluate($this) :
                $this->aliasValues->alias($actionNode->right);
                
            $aliases = [$leftAlias, $rightAlias];
        }

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

        $evaluatedNodes = array_map(fn(EvaluableInterface $action) => $action->evaluate($this), $sequence->actions);
 
        return $type . ' ' . implode(', ', $evaluatedNodes);
    }

    /**
     * @param Update $updateNode
     * @return string
     */
    public function evaluateUpdate(Update $updateNode): string
    {
        $evaluatedActionSequences = array_map(
            fn (EvaluableInterface $node) => $node->evaluate($this),
            $updateNode->sequences
        );

        return implode(' ', $evaluatedActionSequences);
    }

    /**
     * @param Projection $projectionNode
     * @return string
     */
    public function evaluateProjection(Projection $projectionNode): string
    {
        $evaluatedProjections = array_map(
            fn (EvaluableInterface $node) => $node->evaluate($this),
            $projectionNode->attributes
        );

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
