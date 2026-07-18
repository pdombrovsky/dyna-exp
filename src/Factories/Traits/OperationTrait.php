<?php

namespace DynaExp\Factories\Traits;

use DynaExp\Factories\DefaultValue;
use DynaExp\Factories\Attribute;
use DynaExp\Factories\Internal\ExpressionOperandInterface;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\Operation;

trait OperationTrait
{
    abstract protected function evaluable(): EvaluableInterface;

    /**
     * Creates an operation to add a specified value to the attribute.
     *
     * @param Attribute|DefaultValue|mixed $value The value or operation to add.
     * @return Operation
     */
    public function plus(mixed $value): Operation
    {
        return Operation::plus(
            $this->evaluable(),
            $value instanceof ExpressionOperandInterface ? $value->toEvaluable() : $value
        );
    }

    /**
     * Creates an operation to subtract a specified value from the attribute.
     *
     * @param Attribute|DefaultValue|mixed $value The value or operation to subtract.
     * @return Operation
     */
    public function minus(mixed $value): Operation
    {
        return Operation::minus(
            $this->evaluable(),
            $value instanceof ExpressionOperandInterface ? $value->toEvaluable() : $value
        );
    }

    /**
     * Creates an operation to append values to a list attribute.
     *
     * @param Attribute|DefaultValue|mixed $values The values or operation to append.
     * @return Operation
     */
    public function listAppend(mixed $values): Operation
    {
        return Operation::listAppend(
            $this->evaluable(),
            $values instanceof ExpressionOperandInterface ? $values->toEvaluable() : $values
        );
    }

    /**
     * Creates an operation to prepend values to a list attribute.
     *
     * @param Attribute|DefaultValue|mixed $values The values or operation to prepend.
     * @return Operation
     */
    public function listPrepend(mixed $values): Operation
    {
        return Operation::listPrepend(
            $this->evaluable(),
            $values instanceof ExpressionOperandInterface ? $values->toEvaluable() : $values
        );
    }

    /**
     * @inheritDoc
     */
    public function toEvaluable(): EvaluableInterface
    {
        return $this->evaluable();
    }
}
