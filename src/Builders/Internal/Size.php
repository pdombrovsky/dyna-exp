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

     * @param Path $name
     */
    public function __construct(Path $name)
    {
        $this->node = new SizeNode($name->getNode());
    }
}
