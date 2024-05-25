<?php

namespace DynaExp\Builders;

use Aws\DynamoDb\SetValue;
use DynaExp\Builders\Internal\UpdateOperation;
use DynaExp\Enums\UpdateOperationModeEnum;
use DynaExp\Enums\UpdateOperationTypeEnum;
use DynaExp\Interfaces\EvaluatorInterface;
use DynaExp\Interfaces\TreeEvaluatorInterface;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Update;

class UpdateBuilder implements TreeEvaluatorInterface
{
    /**
     * @var array<string, UpdateOperation>
     */
    public readonly array $operationsMap;

    public function __construct()
    {
        $operationsMap = [];

        foreach (UpdateOperationTypeEnum::cases() as $operationType) {

            $operationsMap[$operationType->name] = new UpdateOperation($operationType);
        }

        $this->operationsMap = $operationsMap;
    }

    /**
     * @param Name $name
     * @param int|float|SetValue $value
     * @return UpdateBuilder
     */
    public function add(Name $name, int|float|SetValue $value) : UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::add->name]->add(new Update($name->getCurrentNode(), UpdateOperationModeEnum::add, value: $value));

        return $this;
    }

    /**
     * @param Name $name
     * @param SetValue $value
     * @return UpdateBuilder
     */
    public function delete(Name $name, SetValue $value) : UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::delete->name]->add(new Update($name->getCurrentNode(), UpdateOperationModeEnum::delete, value: $value));

        return $this;
    }

    /**
     * @param Name $name
     * @return UpdateBuilder
     */
    public function remove(Name $name) : UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::remove->name]->add($name->getCurrentNode());

        return $this;
    }

    /**
     * @param Name $name
     * @param mixed $value
     * @return UpdateBuilder
     */
    public function setValue(Name $name, mixed $value) : UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::set->name]->add(
            new Update($name->getCurrentNode(), UpdateOperationModeEnum::setValue, value: $value)
        );

        return $this;
    }

    /**
     * @param Name $name
     * @param Operation $operation
     * @return UpdateBuilder
     */
    public function setOperation(Name $name, Operation $operation): UpdateBuilder
    {
        $this->operationsMap[UpdateOperationTypeEnum::set->name]->add(
            new Update($name->getCurrentNode(), UpdateOperationModeEnum::setValue, $operation)
        );

        return $this;
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluateTree(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluateUpdateBuilderTree($this);
    }
}
