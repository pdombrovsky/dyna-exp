<?php

namespace DynaExp\Factories;

use DynaExp\Factories\Traits\OperationTrait;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\PathNode;

final readonly class IfNotExists implements ExpressionOperandInterface
{
    use OperationTrait;

    private Operation $pathNode;

    /**
     * @param PathNode $path
     * @param mixed $value
     */
    public function __construct(PathNode $path, mixed $value)
    {
        $this->pathNode = Operation::ifNotExists($path, $value);
    }
}
