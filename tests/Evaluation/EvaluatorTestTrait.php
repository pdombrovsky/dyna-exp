<?php

namespace DynaExp\Tests\Evaluation;

use DynaExp\Evaluation\Evaluator;
use DynaExp\Nodes\EvaluableInterface;

trait EvaluatorTestTrait
{
    protected function testEvaluate(EvaluableInterface $node, string $evaluated, array $aliasNames, array $aliasValues)
    {
        $evaluator = new Evaluator();

        $this->assertSame(
            $evaluated,
            $evaluator->evaluate($node)
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
