<?php

namespace DynaExp\Factories\Traits;

use DynaExp\Factories\IfNotExists;
use DynaExp\Factories\Path;
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
            $this->pathNode,
            $value instanceof Path || $value instanceof IfNotExists ? $value->pathNode : $value
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
            $this->pathNode,
            $value instanceof Path || $value instanceof IfNotExists ? $value->pathNode : $value
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
            $this->pathNode,
            $values instanceof Path || $values instanceof IfNotExists ? $values->pathNode : $values
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
            $this->pathNode,
            $values instanceof Path || $values instanceof IfNotExists ? $values->pathNode : $values
        );
    }
}
