<?php

namespace DynaExp\Factories\Traits;

use DynaExp\Factories\ExpressionOperandInterface;
use DynaExp\Factories\IfNotExists;
use DynaExp\Factories\Path;
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
        return Operation::plus(
            $this->pathNode,
            $value instanceof ExpressionOperandInterface ? $value->toNode() : $value
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
        return Operation::minus(
            $this->pathNode,
            $value instanceof ExpressionOperandInterface ? $value->toNode() : $value
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
        return Operation::listAppend(
            $this->pathNode,
            $values instanceof ExpressionOperandInterface ? $values->toNode() : $values
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
        return Operation::listPrepend(
            $this->pathNode,
            $values instanceof ExpressionOperandInterface ? $values->toNode() : $values
        );
    }

    /**
     * @inheritDoc
     */
    public function toNode(): EvaluableInterface
    {
        return $this->pathNode;
    }
}
