<?php

namespace DynaExp\Factories;

use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Factories\Abstracts\AbstractNode;
use DynaExp\Factories\Traits\OperationTrait;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Path;

final readonly class IfNotExists extends AbstractNode
{
    use OperationTrait;

    private Operation $pathNode;

    /**
     * @param Path $path
     * @param mixed $value
     */
    public function __construct(Path $path, mixed $value)
    {
        $this->pathNode = new Operation(OperationTypeEnum::ifNotExists, $path, $value);
    }
}
