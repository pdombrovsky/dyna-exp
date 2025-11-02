<?php

namespace DynaExp\Tests\Builders;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Factories\Path;
use PHPUnit\Framework\TestCase;

final class ConditionBuilderStaticCtorsTest extends TestCase
{
    public function testAllOfCreatesAndCondition(): void
    {
        $c1 = Path::create('a')->equal(1);
        $c2 = Path::create('b')->greaterThan(2);

        $built = ConditionBuilder::allOf($c1, $c2)->build();
        $this->assertSame(ConditionTypeEnum::andCond, $built->type);
        $this->assertCount(2, $built->nodes);
    }

    public function testAnyOfCreatesOrCondition(): void
    {
        $c1 = Path::create('x')->attributeExists();
        $c2 = Path::create('y')->notEqual(0);

        $built = ConditionBuilder::anyOf($c1, $c2)->build();
        $this->assertSame(ConditionTypeEnum::orCond, $built->type);
        $this->assertCount(2, $built->nodes);
    }
}

