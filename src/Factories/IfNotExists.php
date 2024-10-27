<?php

namespace DynaExp\Factories;

use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Factories\Traits\OperationTrait;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Path;

final class IfNotExists
{
    use OperationTrait;

    public Operation $pathNode;

    /**
     * @param Path $path
     * @param mixed $value
     */
    public function __construct(Path $path, mixed $value)
    {
        $this->pathNode = new Operation(OperationTypeEnum::ifNotExists, $path, $value);
    }
}
