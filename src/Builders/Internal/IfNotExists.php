<?php

namespace DynaExp\Builders\Internal;

use DynaExp\Builders\Path;
use DynaExp\Builders\Traits\NodeTrait;
use DynaExp\Builders\Traits\OperationTrait;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Interfaces\NodeInterface;
use DynaExp\Nodes\Operation;

final class IfNotExists implements NodeInterface
{
    use NodeTrait;
    use OperationTrait;

    /**
     * @param Path $name
     * @param mixed $value
     */
    public function __construct(Path $name, mixed $value)
    {
        $this->node = new Operation($name->getNode(), OperationTypeEnum::ifNotExists, $value);
    }
}
