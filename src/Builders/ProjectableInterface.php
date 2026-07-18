<?php

namespace DynaExp\Builders;

use DynaExp\Nodes\Path;

interface ProjectableInterface
{
    /**
     * @return Path
     */
    function project(): Path;
}
