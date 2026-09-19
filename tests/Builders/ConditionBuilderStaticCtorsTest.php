<?php

namespace DynaExp\Tests\Builders;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Factories\Attribute;
use PHPUnit\Framework\TestCase;

final class ConditionBuilderStaticCtorsTest extends TestCase
{
    public function testAllOfCreatesAndCondition(): void
    {
        $c1 = Attribute::create('a')->equal(1);
        $c2 = Attribute::create('b')->greaterThan(2);

        $built = ConditionBuilder::allOf($c1, $c2)->build();
        $this->assertSame(ConditionTypeEnum::andCond, $built->type);
        $this->assertSame($c1, $built->firstOperand());
        $this->assertSame([$c2], $built->tailOperands());
    }

    public function testAnyOfCreatesOrCondition(): void
    {
        $c1 = Attribute::create('x')->attributeExists();
        $c2 = Attribute::create('y')->notEqual(0);

        $built = ConditionBuilder::anyOf($c1, $c2)->build();
        $this->assertSame(ConditionTypeEnum::orCond, $built->type);
        $this->assertSame($c1, $built->firstOperand());
        $this->assertSame([$c2], $built->tailOperands());
    }
}

