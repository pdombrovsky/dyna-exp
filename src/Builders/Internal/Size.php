<?php

namespace DynaExp\Builders\Internal;

use DynaExp\Builders\Path;
use DynaExp\Builders\Traits\ConditionTrait;
use DynaExp\Builders\Traits\NodeTrait;
use DynaExp\Nodes\Size as SizeNode;

final class Size
{
    use ConditionTrait;
    use NodeTrait;

    /**

     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->node = new SizeNode($path->getNode());
    }
}
