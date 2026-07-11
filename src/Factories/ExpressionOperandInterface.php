<?php

namespace DynaExp\Factories;

use DynaExp\Nodes\EvaluableInterface;

interface ExpressionOperandInterface
{
    public function toNode(): EvaluableInterface;
}
