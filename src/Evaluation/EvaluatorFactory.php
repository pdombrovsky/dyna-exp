<?php

namespace DynaExp\Evaluation;

use DynaExp\Evaluation\Evaluator;

class EvaluatorFactory implements EvaluatorFactoryInterface
{
    /**
     * @return EvaluatorInterface
     */
    public function make(): EvaluatorInterface
    {
       return new Evaluator();
    }
}
