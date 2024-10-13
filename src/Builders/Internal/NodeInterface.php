<?php

namespace DynaExp\Builders\Internal;

use DynaExp\Nodes\EvaluableInterface;

interface NodeInterface
{
    /**
     * @return EvaluableInterface
     */
    function getNode(): EvaluableInterface;
}
