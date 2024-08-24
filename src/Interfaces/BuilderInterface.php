<?php

namespace DynaExp\Interfaces;

use DynaExp\Interfaces\EvaluableInterface;

interface BuilderInterface
{
    /**
     * @return ?EvaluableInterface
     */
    function build(): ?EvaluableInterface;
}
