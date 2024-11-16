<?php

namespace DynaExp\Evaluation;

interface EvaluatorFactoryInterface
{
    /**
     * @return EvaluatorInterface
     */
    function make(): EvaluatorInterface;
}
