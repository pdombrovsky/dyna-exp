<?php

namespace DynaExp\Tests\Evaluation;

use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Evaluation\ExpressionPreprocessorInterface;
use DynaExp\Factories\Attribute;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Nodes\Path;
use PHPUnit\Framework\TestCase;

final class EvaluatorTransformerTest extends TestCase
{
    public function testPreprocessorCanNormalizeCurrentNodeBeforeRendering(): void
    {
        $preprocessor = new class () implements ExpressionPreprocessorInterface {
            public function process(EvaluableInterface $node): EvaluableInterface
            {
                if (! $node instanceof Condition || $node->type !== ConditionTypeEnum::equalCond) {
                    return $node;
                }

                $left = $node->firstOperand();
                $right = $node->tailOperands()[0] ?? null;

                if (! $left instanceof Path || $left->segments !== ['status'] || ! is_string($right)) {
                    return $node;
                }

                $normalized = strtoupper($right);

                return $normalized === $right
                    ? $node
                    : Condition::equal($left, $normalized);
            }
        };

        $result = (new ExpressionBuilder($preprocessor))
            ->setFilter(Attribute::create('status')->equal('active'))
            ->build()
            ->toArray();

        $this->assertSame('#0 = :0', $result['FilterExpression']);
        $this->assertSame(['#0' => 'status'], $result['ExpressionAttributeNames']);
        $this->assertSame([':0' => 'ACTIVE'], $result['ExpressionAttributeValues']);
    }
}
