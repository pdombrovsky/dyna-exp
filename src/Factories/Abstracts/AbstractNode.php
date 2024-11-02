<?php

namespace DynaExp\Factories\Abstracts;

use DynaExp\Nodes\EvaluableInterface;

abstract readonly class AbstractNode
{
    abstract protected function getNode(): EvaluableInterface;
}
