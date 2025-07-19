<?php

namespace DynaExp\Builders;

use DynaExp\Nodes\PathNode;

interface ProjectableInterface
{
    /**
     * @return PathNode
     */
    function project(): PathNode;
}
