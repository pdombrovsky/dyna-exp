<?php

namespace DynaExp\Factories\Traits;

use DynaExp\Factories\Abstracts\AbstractNode;
use DynaExp\Factories\IfNotExists;
use DynaExp\Factories\Path;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\Operation;

trait OperationTrait
{
    /**
     * Creates an operation to add a specified value to the attribute.
     *
     * @param Path|IfNotExists|mixed $value The value or operation to add.
     * @return Operation
     */
    public function plus(mixed $value): Operation
    {
        return new Operation(
            OperationTypeEnum::plusValue,
            $this->pathNode,
            $value instanceof AbstractNode ? $value->getNode() : $value
        );
    }

    /**
     * Creates an operation to subtract a specified value from the attribute.
     *
     * @param Path|IfNotExists|mixed $value The value or operation to subtract.
     * @return Operation
     */
    public function minus(mixed $value): Operation
    {
        return new Operation(
            OperationTypeEnum::minusValue,
            $this->pathNode,
            $value instanceof AbstractNode ? $value->getNode() : $value
        );
    }

    /**
     * Creates an operation to append values to a list attribute.
     *
     * @param Path|IfNotExists|mixed $values The values or operation to append.
     * @return Operation
     */
    public function listAppend(mixed $values): Operation
    {
        return new Operation(
            OperationTypeEnum::listAppend,
            $this->pathNode,
            $values instanceof AbstractNode ? $values->getNode() : $values
        );
    }

    /**
     * Creates an operation to prepend values to a list attribute.
     *
     * @param Path|IfNotExists|mixed $values The values or operation to prepend.
     * @return Operation
     */
    public function listPrepend(mixed $values): Operation
    {
        return new Operation(
            OperationTypeEnum::listPrepend,
            $this->pathNode,
            $values instanceof AbstractNode ? $values->getNode() : $values
        );
    }

    /**
     * @inheritDoc
     */
    protected function getNode(): EvaluableInterface
    {
        return $this->pathNode;
    }
}
