<?php

namespace DynaExp\Builders;

use DynaExp\Builders\Traits\ConditionTrait;
use DynaExp\Nodes\Size as SizeNode;

class Size
{
    use ConditionTrait;

    /**

     * @param Name $name
     */
    public function __construct(Name $name)
    {
        $this->currentNode = new SizeNode($name->getCurrentNode());
    }

    /**
     * @return SizeNot
     */
    public function not(): SizeNot
    {
        return new SizeNot($this);
    }
}
