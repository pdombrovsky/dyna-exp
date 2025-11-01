<?php

namespace DynaExp\Tests\Builders;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Evaluation\Evaluator;
use DynaExp\Factories\Path;
use PHPUnit\Framework\TestCase;

final class ConditionBuilderStressTest extends TestCase
{
    public function testChainsManyConditionsAndNestedBuilders(): void
    {
        $metrics = [];
        for ($i = 0; $i < 5; $i++) {
            $metrics[] = Path::create('metric' . $i);
        }

        $builder = new ConditionBuilder($metrics[0]->greaterThan(0));
        for ($i = 1; $i < count($metrics); $i++) {
            $builder->and($metrics[$i]->greaterThan($i));
        }

        $status = Path::create('status');
        $flag = Path::create('flag');

        $builder->and(
            ConditionBuilder::anyOf(
                $status->equal('A'),
                $status->equal('B'),
                ConditionBuilder::allOf(
                    $flag->attributeExists(),
                    $flag->notEqual(false)
                )
            )
        );

        $condition = $builder->build();

        $evaluator = new Evaluator();
        $evaluated = $condition->evaluate($evaluator);

        $this->assertSame(
            '#0 > :0 AND #1 > :1 AND #2 > :2 AND #3 > :3 AND #4 > :4 AND (#5 = :5 OR #5 = :6 OR (attribute_exists (#6) AND #6 <> :7))',
            $evaluated
        );

        $this->assertSame(
            [
                '#0' => 'metric0',
                '#1' => 'metric1',
                '#2' => 'metric2',
                '#3' => 'metric3',
                '#4' => 'metric4',
                '#5' => 'status',
                '#6' => 'flag',
            ],
            $evaluator->getAttributeNameAliases()
        );

        $this->assertSame(
            [
                ':0' => 0,
                ':1' => 1,
                ':2' => 2,
                ':3' => 3,
                ':4' => 4,
                ':5' => 'A',
                ':6' => 'B',
                ':7' => false,
            ],
            $evaluator->getAttributeValueAliases()
        );
    }
}
