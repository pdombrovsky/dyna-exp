<?php

namespace DynaExp\Factories\Internal;

use DynaExp\Nodes\EvaluableInterface;

/**
 * @internal
 */
interface ExpressionOperandInterface
{
    public function toEvaluable(): EvaluableInterface;
}
