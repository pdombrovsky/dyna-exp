<?php

namespace DynaExp\Builders\Traits;

use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Nodes\Operation;

trait SetOperationTrait
{
    /**
     * @param int|float $value
     * @return Operation
     */
    public function plus(int|float $value): Operation
    {
        return new Operation($this->currentNode, OperationTypeEnum::plusValue, value: $value);
    }

    /**
     * @param int|float $value
     * @return Operation
     */
    public function minus(int|float $value): Operation
    {
        return new Operation($this->currentNode, OperationTypeEnum::minusValue, value: $value);
    }

    /**
     * @param array $values
     * @return Operation
     */
    public function listAppend(array $values): Operation
    {
        return new Operation($this->currentNode, OperationTypeEnum::listAppend, value: $values);
    }

    /**
     * @param array $values
     * @return Operation
     */
    public function listPrepend(array $values): Operation
    {
        return new Operation($this->currentNode, OperationTypeEnum::listPrepend, value: $values);
    }
}
