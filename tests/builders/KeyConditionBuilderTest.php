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

    public function testAndCanOnlyBeCalledOnce(): void
    {
        $builder = new KeyConditionBuilder(Key::create('pk')->equal('USER#1'));
        $builder->and(Key::create('sk')->beginsWith('ORDER#'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only one AND key condition is allowed.');

        $builder->and(Key::create('sk2')->equal('OTHER'));
    }

    public function testNestedAndConditionIsRejected(): void
    {
        $nested = KeyCondition::and(
            Key::create('pk')->equal('USER#1'),
            Key::create('sk')->beginsWith('ORDER#')
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Condition 'AND' must not be nested.");

        new KeyConditionBuilder($nested);
    }
}
