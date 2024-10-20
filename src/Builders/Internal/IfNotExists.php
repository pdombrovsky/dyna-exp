<?php

namespace DynaExp\Builders\Internal;

use DynaExp\Builders\Internal\NodeInterface;
use DynaExp\Builders\Path;
use DynaExp\Builders\Traits\NodeTrait;
use DynaExp\Builders\Traits\OperationTrait;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Nodes\Operation;

final class IfNotExists implements NodeInterface
{
    use NodeTrait;
    use OperationTrait;

    /**
     * @param Path $path
     * @param mixed $value
     */
    public function __construct(Path $path, mixed $value)
    {
        $this->node = new Operation(OperationTypeEnum::ifNotExists, $path->getNode(), $value);
    }
}
