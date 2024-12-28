<?php

namespace DynaExp\Tests\Evaluation;

use DynaExp\Evaluation\Evaluator;
use DynaExp\Nodes\EvaluableInterface;

trait EvaluatorTestTrait
{
    protected function testEvaluate(EvaluableInterface $evaluable, string $evaluated, array $aliasNames, array $aliasValues)
    {
        $evaluator = new Evaluator();

        $this->assertSame(
            $evaluated,
            $evaluable->evaluate($evaluator)
        );

        $this->assertSame(
            $aliasNames,
            $evaluator->getAttributeNameAliases()
        );

        $this->assertSame(
            $aliasValues,
            $evaluator->getAttributeValueAliases()
        );
    }
}
