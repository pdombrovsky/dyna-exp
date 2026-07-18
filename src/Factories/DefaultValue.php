<?php

namespace DynaExp\Factories;

use DynaExp\Factories\Internal\ExpressionOperandInterface;
use DynaExp\Factories\Traits\OperationTrait;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Path;

final readonly class DefaultValue implements ExpressionOperandInterface
{
    use OperationTrait;

    private Operation $evaluable;

    /**
     * @param Path $path
     * @param mixed $value
     */
    public function __construct(Path $path, mixed $value)
    {
        $this->evaluable = Operation::ifNotExists($path, $value);
    }

    /**
     * @return Operation
     */
    protected function evaluable(): Operation
    {
        return $this->evaluable;
    }
}
