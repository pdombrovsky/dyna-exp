<?php

namespace DynaExp\Factories;

use DynaExp\Factories\Traits\ConditionTrait;
use DynaExp\Nodes\Path;
use DynaExp\Nodes\Size as SizeNode;

final class Size
{
    use ConditionTrait;

    public SizeNode $pathNode;

    /**
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->pathNode = new SizeNode($path);
    }
}
