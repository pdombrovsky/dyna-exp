<?php

namespace DynaExp\Interfaces;

interface NodeInterface
{
    /**
     * @return EvaluableInterface
     */
    function getNode(): EvaluableInterface;
}
