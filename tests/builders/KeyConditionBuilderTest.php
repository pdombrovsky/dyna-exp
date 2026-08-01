<?php

namespace DynaExp\Tests\Builders;

use DynaExp\Builders\KeyConditionBuilder;
use DynaExp\Evaluation\Evaluator;
use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Factories\Key;
use DynaExp\Nodes\KeyCondition;
use PHPUnit\Framework\TestCase;

final class KeyConditionBuilderTest extends TestCase
{
    public function testBuildsSingleCondition(): void
    {
        $condition = (new KeyConditionBuilder(Key::create('pk')->equal('USER#1')))
            ->build();

        $this->assertSame('#0 = :0', (new Evaluator())->evaluate($condition));
    }

    public function testBuildsTwoConditionsWhenOneSideIsEquality(): void
    {
        $condition = KeyConditionBuilder::allOf(
            Key::create('pk')->equal('USER#1'),
            Key::create('sk')->beginsWith('ORDER#')
        )->build();

        $evaluator = new Evaluator();
        $this->assertSame('#0 = :0 AND begins_with (#1, :1)', $evaluator->evaluate($condition));
    }

    public function testAndDoesNotValidateTableSchema(): void
    {
        $condition = KeyConditionBuilder::allOf(
            Key::create('sk')->greaterThan('ORDER#1'),
            Key::create('sk')->lessThan('ORDER#9')
        )->build();

        $evaluator = new Evaluator();
        $this->assertSame('#0 > :0 AND #0 < :1', $evaluator->evaluate($condition));
    }

    public function testAndCanBeCalledMultipleTimes(): void
    {
        $pk1 = Key::create('pk1')->equal('USER#1');
        $pk2 = Key::create('pk2')->equal('REGION#EU');
        $sk1 = Key::create('sk1')->equal('ORDER');
        $sk2 = Key::create('sk2')->beginsWith('2026#');

        $condition = (new KeyConditionBuilder($pk1))
            ->and($pk2)
            ->and($sk1)
            ->and($sk2)
            ->build();

        $this->assertEquals(
            KeyCondition::and(
                KeyCondition::and(
                    KeyCondition::and($pk1, $pk2),
                    $sk1
                ),
                $sk2
            ),
            $condition
        );
    }

    public function testNestedAndConditionIsRejected(): void
    {
        $nested = KeyCondition::and(
            Key::create('pk')->equal('USER#1'),
            Key::create('sk')->beginsWith('ORDER#')
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Key condition of type 'AND' cannot be added to the builder."
        );

        new KeyConditionBuilder($nested);
    }

    public function testNestedAndConditionCannotBeAdded(): void
    {
        $nested = KeyCondition::and(
            Key::create('pk2')->equal('REGION#EU'),
            Key::create('sk')->beginsWith('ORDER#')
        );

        $builder = new KeyConditionBuilder(
            Key::create('pk1')->equal('USER#1')
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Key condition of type 'AND' cannot be added to the builder."
        );

        $builder->and($nested);
    }
}
