<?php

namespace DynaExp\Factories;

use DynaExp\Factories\Traits\ConditionTrait;
use DynaExp\Nodes\PathNode;
use DynaExp\Nodes\Size as SizeNode;

final class Size
{
    use ConditionTrait;

    private SizeNode $pathNode;

    /**
     * @param PathNode $path
     */
    public function __construct(PathNode $path)
    {
        $this->pathNode = new SizeNode([$path]);
    }
}
