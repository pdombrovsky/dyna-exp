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
     * @param Path|IfNotExists|mixed $value
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
     * @param Path|IfNotExists|mixed $value
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
     * @param Path|IfNotExists|array<mixed> $values
     * @return Operation
     */
    public function listAppend(Path|IfNotExists|array $values): Operation
    {
        return new Operation(
            OperationTypeEnum::listAppend,
            $this->pathNode,
            $values instanceof AbstractNode ? $values->getNode() : $values
        );
    }

    /**
     * @param Path|IfNotExists|array<mixed> $values
     * @return Operation
     */
    public function listPrepend(Path|IfNotExists|array $values): Operation
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
