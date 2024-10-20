<?php

namespace DynaExp\Builders\Traits;

use DynaExp\Builders\Internal\IfNotExists;
use DynaExp\Builders\Internal\NodeInterface;
use DynaExp\Builders\Path;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Nodes\Operation;

trait OperationTrait
{
    /**
     * @param Path|IfNotExists|mixed $value
     * @return Operation
     */
    public function plus(mixed $value): Operation
    {
        return new Operation(
            OperationTypeEnum::plusValue,
            $this->node,
            $value instanceof NodeInterface ? $value->getNode() : $value
        );
    }

    /**
     * @param Path|IfNotExists|mixed $value
     * @return Operation
     */
    public function minus(mixed $value): Operation
    {
        return new Operation(
            OperationTypeEnum::minusValue,
            $this->node,
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
            OperationTypeEnum::listAppend,
            $this->node,
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
            OperationTypeEnum::listPrepend,
            $this->node,
            $values instanceof NodeInterface ? $values->getNode() : $values
        );
    }
}
