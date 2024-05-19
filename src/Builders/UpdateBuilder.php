<?php

namespace DynaExp\Builders;

use Aws\DynamoDb\SetValue;
use DynaExp\Enums\UpdateOperationModeEnum;
use DynaExp\Enums\UpdateOperationTypeEnum;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Interfaces\NodeEvaluatorInterface;
use DynaExp\Interfaces\TreeEvaluatorInterface;

use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Update;

class UpdateBuilder implements TreeEvaluatorInterface
{
    /**
     * @var array<string, array>
     */
    private array $operationsMap;

    public function __construct()
    {
        foreach (UpdateOperationTypeEnum::cases() as $operationType) {

            $this->operationsMap[$operationType->name] = [];
        }
    }

    /**
     * @param Name $name
     * @param int|float|SetValue $value
     * @return UpdateBuilder
     */
    public function add(Name $name, int|float|SetValue $value) : UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::add->name][] = new Update($name->getCurrentNode(), UpdateOperationModeEnum::add, value: $value);

        return $this;
    }

    /**
     * @param Name $name
     * @param SetValue $value
     * @return UpdateBuilder
     */
    public function delete(Name $name, SetValue $value) : UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::delete->name][] = new Update($name->getCurrentNode(), UpdateOperationModeEnum::delete, value: $value);

        return $this;
    }

    /**
     * @param Name $name
     * @return UpdateBuilder
     */
    public function remove(Name $name) : UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::remove->name][] = $name->getCurrentNode();

        return $this;
    }

    /**
     * @param Name $name
     * @param mixed $value
     * @return UpdateBuilder
     */
    public function setValue(Name $name, mixed $value) : UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::set->name][] =
            new Update($name->getCurrentNode(), UpdateOperationModeEnum::setValue, value: $value);

        return $this;
    }

    /**
     * @param Name $name
     * @param Operation $operation
     * @return UpdateBuilder
     */
    public function setOperation(Name $name, Operation $operation): UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::set->name][] =
            new Update($name->getCurrentNode(), UpdateOperationModeEnum::setValue, $operation);

        return $this;
    }

    /**
     * @param NodeEvaluatorInterface $evaluator
     * @return string
     */
    public function evaluateTree(NodeEvaluatorInterface $nodeEvaluator): string
    {
        $formattedExpressions = [];

        foreach (UpdateOperationTypeEnum::cases() as $operationType) {

            $operationNodes = $this->operationsMap[$operationType->name];

            if (empty($operationNodes)) {
                
                continue;
            }

            $evaluatedNodes = array_map(fn(EvaluatedNodeInterface $node) => $node->evaluate($nodeEvaluator), $operationNodes);

            $formattedExpressions[] = "$operationType->value" . ' ' . implode(', ', $evaluatedNodes);
        }

        return empty($formattedExpressions) ? '' : implode(' ', $formattedExpressions);
    }
}
