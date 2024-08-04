<?php

namespace DynaExp\Builders\Internal;

use DynaExp\Builders\Internal\SizeNot;
use DynaExp\Builders\Path;
use DynaExp\Builders\Traits\ConditionTrait;
use DynaExp\Nodes\Size as SizeNode;

final class Size
{
    use ConditionTrait;

    /**

     * @param Path $name
     */
    public function __construct(Path $name)
    {
        $this->node = new SizeNode($name->getNode());
    }

    /**
     * @return SizeNot
     */
    public function not(): SizeNot
    {
        return new SizeNot($this);
    }
}
