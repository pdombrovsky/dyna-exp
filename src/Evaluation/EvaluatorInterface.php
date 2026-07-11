<?php

namespace DynaExp\Evaluation;

use DynaExp\Nodes\EvaluableInterface;

interface EvaluatorInterface
{
    /**
     * Evaluates an expression node in the current aliasing context.
     */
    public function evaluate(EvaluableInterface $node): string;

    /**
     * Allocates an attribute-name alias.
     */
    public function aliasName(string $name): string;

    /**
     * Allocates an attribute-value alias.
     */
    public function aliasValue(mixed $value): string;
}
