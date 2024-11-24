<?php

namespace DynaExp\Evaluation;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Evaluation\Aliases\Names;
use DynaExp\Evaluation\Aliases\Values;
use DynaExp\Exceptions\RuntimeException;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Path;
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
     * @param Path $path
     * @return string
     */
    public function evaluatePath(Path $path): string
    {
        $parts = [];
        $lastIndex = -1;
        foreach ($path->segments as $segment) {

            if (is_int($segment)) {

                $parts[$lastIndex] .= "[$segment]";

            } else {

                $parts[++$lastIndex] = $this->aliasNames->alias($segment);
            }
        }
        
        return implode('.', $parts);
    }

    /**
     * @param Size $size
     * @return string
     */
    public function evaluateSize(Size $size): string
    {
        return "size ({$size->node->evaluate($this)})";
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

        $aliases = $this->evaluateNodeOrAliasValue($conditionNode->nodes);
 
        return sprintf($fmtString, ...$aliases);
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

        $aliases = $this->evaluateNodeOrAliasValue($keyConditionNode->nodes);
 
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

        $aliases = $this->evaluateNodeOrAliasValue($operation->nodes);

        if (OperationTypeEnum::listPrepend == $operation->type) {

            $aliases = array_reverse($aliases);

        }

        return sprintf($fmtString, ...$aliases);
    }

    /**
     * @param Action $actionNode
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

        $aliases = $this->evaluateNodeOrAliasValue($actionNode->nodes);
 
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

        $evaluatedNodes = $this->evaluateNodeOrAliasValue($sequence->actions);
 
        return $type . ' ' . implode(', ', $evaluatedNodes);
    }

    /**
     * @param Update $updateNode
     * @return string
     */
    public function evaluateUpdate(Update $updateNode): string
    {
        $evaluatedActionSequences = $this->evaluateNodeOrAliasValue($updateNode->sequences);

        return implode(' ', $evaluatedActionSequences);
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

        return implode(', ', $evaluatedProjections);
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
