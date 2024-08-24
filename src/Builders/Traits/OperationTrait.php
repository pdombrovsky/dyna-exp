<?php

namespace DynaExp\Builders\Traits;

use DynaExp\Builders\Internal\IfNotExists;
use DynaExp\Builders\Path;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Interfaces\NodeInterface;
use DynaExp\Nodes\Operation;

trait OperationTrait
{
    /**
     * @param Path|IfNotExists|int|float $value
     * @return Operation
     */
    public function plus(Path|IfNotExists|int|float $value): Operation
    {
        return new Operation(
            $this->node,
            OperationTypeEnum::plusValue,
            $value instanceof NodeInterface ? $value->getNode() : $value
        );
    }

    /**
     * @param Path|IfNotExists|int|float $value
     * @return Operation
     */
    public function minus(Path|IfNotExists|int|float $value): Operation
    {
        return new Operation(
            $this->node,
            OperationTypeEnum::minusValue,
            $value instanceof NodeInterface ? $value->getNode() : $value
        );
    }

    /**
     * @param Path|IfNotExists|array $values
     * @return Operation
     */
    public function listAppend(Path|IfNotExists|array $values): Operation
    {
        return new Operation(
            $this->node,
            OperationTypeEnum::listAppend,
            $values instanceof NodeInterface ? $values->getNode() : $values
        );
    }

    /**
     * @param Path|IfNotExists|array $values
     * @return Operation
     */
    public function listPrepend(Path|IfNotExists|array $values): Operation
    {
        return new Operation(
            $this->node,
            OperationTypeEnum::listPrepend,
            $values instanceof NodeInterface ? $values->getNode() : $values
        );
    }
}
