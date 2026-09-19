<?php

namespace DynaExp\Tests\Result;

use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Result\ExpressionResult;
use PHPUnit\Framework\TestCase;

final class ExpressionResultTest extends TestCase
{
    public function testHasDetectsKnownComponent(): void
    {
        $result = new ExpressionResult([
            ExpressionTypeEnum::filter->value => 'cond',
            ExpressionTypeEnum::values->value => [':0' => 1],
        ]);

        $this->assertTrue($result->has(ExpressionTypeEnum::filter));
        $this->assertTrue($result->has(ExpressionTypeEnum::values));
        $this->assertFalse($result->has(ExpressionTypeEnum::update));
    }

    public function testToArrayReturnsStoredComponents(): void
    {
        $components = [
            ExpressionTypeEnum::filter->value => 'cond',
            ExpressionTypeEnum::values->value => [':0' => 1, ':1' => 's'],
        ];

        $result = new ExpressionResult($components);

        $this->assertSame($components, $result->toArray());
    }
}
