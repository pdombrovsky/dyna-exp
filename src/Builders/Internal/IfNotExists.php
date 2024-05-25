<?php

namespace DynaExp\Builders\Internal;

use DynaExp\Builders\Name;
use DynaExp\Builders\Traits\SetOperationTrait;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Interfaces\EvaluatedNodeInterface;
use DynaExp\Nodes\Operation;

class IfNotExists
{
    use SetOperationTrait;

    /**
     * @var EvaluatedNodeInterface
     */
    private EvaluatedNodeInterface $currentNode;

    /**
     * @param Name $name
     * @param mixed $value
     */
    public function __construct(Name $name, mixed $value)
    {
        $this->currentNode = new Operation($name->getCurrentNode(), OperationTypeEnum::ifNotExists, value: $value);
    }
}
