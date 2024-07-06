<?php

namespace DynaExp\Evaluation;

use DynaExp\Builders\Internal\UpdateOperation;
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Enums\UpdateOperationModeEnum;
use DynaExp\Enums\UpdateOperationTypeEnum;
use DynaExp\Evaluation\Names;
use DynaExp\Evaluation\Values;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\EvaluatorInterface;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\Dot;
use DynaExp\Nodes\Index;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Name;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Size;
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
     * @param string $name
     * @return string
     */
    public function evaluateName(Name $nameNode): string
    {
        return $this->aliasNames->alias($nameNode->name);
    }

    /**
     * @param Size $sizeNode
     * @return string
     */
    public function evaluateSize(Size $sizeNode): string
    {
        return  "size ({$sizeNode->node->evaluate($this)})";
    }

    /**
     * @param Index $indexNode
     * @return string
     */
    public function evaluateIndex(Index $indexNode): string
    {
        return $indexNode->node->evaluate($this) . "[$indexNode->value]";
    }

    /**
     * @param Dot $dotNode
     * @return string
     */
    public function evaluateDot(Dot $dotNode): string
    {
        return $dotNode->left->evaluate($this) . '.' . $dotNode->right->evaluate($this);
    }

    /**
     * @param Condition $conditionNode
     * @return string
     */
    public function evaluateCondition(Condition $conditionNode): string
    {
        $fmtString = match($conditionNode->type) {

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

        $secondAlias = match($conditionNode->type) {

            ConditionTypeEnum::attrExistsCond,
            ConditionTypeEnum::attrNotExistsCond,
            ConditionTypeEnum::parenthesesCond,
            ConditionTypeEnum::notCond => [],
            ConditionTypeEnum::andCond,
            ConditionTypeEnum::orCond => [$conditionNode->right->evaluate($this)],

            default => array_map(fn(mixed $value) => $this->aliasValues->alias($value), $conditionNode->values),
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
            KeyConditionTypeEnum::lessThanKeyCond  => '%s < %s',
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
            array_map(fn(mixed $value) => $this->aliasValues->alias($value), $keyConditionNode->values);


        return sprintf($fmtString, $firstAlias, ...$secondAlias);
    }

    /**
     * @param Operation $operationNode
     * @throws RuntimeException
     * @return string
     */
    public function evaluateOperation(Operation $operationNode): string
    {
        $fmtString = match($operationNode->type) {

            OperationTypeEnum::plusValue => '%s + %s',
            OperationTypeEnum::minusValue => '%s - %s',
            OperationTypeEnum::ifNotExists => 'if_not_exists(%s, %s)',
            OperationTypeEnum::listAppend ,
            OperationTypeEnum::listPrepend => 'list_append(%s, %s)',

            default => throw new RuntimeException("Operation type is unknown"),
        };

        $leftAlias = $operationNode->node->evaluate($this);
        $rightAlias = $this->aliasValues->alias($operationNode->value);

        $aliases = (OperationTypeEnum::listPrepend == $operationNode->type) ?
            [$rightAlias, $leftAlias] :
            [$leftAlias, $rightAlias];

        return sprintf($fmtString, ...$aliases); 
    }

    /**
     * @param Update $operationNode
     * @throws RuntimeException
     * @return string
     */
    public function evaluateUpdate(Update $updateNode): string
    {
        $fmtString = match($updateNode->mode) {

            UpdateOperationModeEnum::setValue => '%s = %s',
            UpdateOperationModeEnum::add,
            UpdateOperationModeEnum::delete => '%s %s',

            default => throw new RuntimeException("Update mode is unknown"),
        };

        $leftAlias = $updateNode->left->evaluate($this);
        $rightAlias = $updateNode->right ? $updateNode->right->evaluate($this) : $this->aliasValues->alias($updateNode->value);

        return sprintf($fmtString, $leftAlias, $rightAlias); 
    }

    /**
     * @param UpdateOperation $updateOperation
     * @throws RuntimeException
     * @return string
     */
    public function evaluateUpdateOperation(UpdateOperation $updateOperation): string
    {
        $operations = $updateOperation->getOperations();

        if (empty($operations)) {
            return '';
        }

        $evaluatedNodes = array_map(fn(EvaluatedNodeInterface $node) => $node->evaluate($this), $operations);

        $operation = match($updateOperation->type) {

            UpdateOperationTypeEnum::set => 'SET',
            UpdateOperationTypeEnum::add => 'ADD',
            UpdateOperationTypeEnum::remove => 'REMOVE',
            UpdateOperationTypeEnum::delete => 'DELETE',

            default => throw new RuntimeException("Update operation type is unknown"),
        };

        return "$operation " . implode(', ', $evaluatedNodes);
    }

    /**
     * @param UpdateBuilder $updateBuilder
     * @return string
     */
    public function evaluateUpdateBuilderTree(UpdateBuilder $updateBuilder): string
    {
        $expressions = [];

        foreach ($updateBuilder->operationsMap as $operations) {

            $currentExpression = $operations->evaluateTree($this);

            if (!empty($currentExpression)) {

                $expressions[] = $currentExpression;
            }
           
        }

        return empty($expressions) ? '' : implode(' ', $expressions);
    }

    /**
     * @param ProjectionBuilder $projectionBuilder
     * @return string
     */
    public function evaluateProjection(ProjectionBuilder $projectionBuilder): string
    {
        $evaluatedNodes = array_map(fn(EvaluatedNodeInterface $node) => $node->evaluate($this), $projectionBuilder->getNames());

        return empty($evaluatedNodes) ? '' : implode(', ', $evaluatedNodes);
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        return $this->aliasNames->getMap();
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->aliasValues->getMap();
    }
}
