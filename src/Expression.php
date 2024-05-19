<?php

namespace DynaExp;

readonly class Expression
{
    public function __construct(public array $expressionMap, public array $namesMap, public array $valuesMap)
    {
    }
}

