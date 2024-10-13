<?php

namespace DynaExp\Builders\Traits;

use DynaExp\Nodes\EvaluableInterface;

trait NodeTrait
{
    /**
     * @var EvaluableInterface
     */
    private EvaluableInterface $node;

    /**
     * @return EvaluableInterface
     */
    public function getNode(): EvaluableInterface
    {
        return $this->node;
    }
}
