<?php

namespace DynaExp\Tests\Context;

use DynaExp\Context\ExpressionContext;
use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Exceptions\UnexpectedValueException;
use PHPUnit\Framework\TestCase;

final class ExpressionContextTest extends TestCase
{
    public function testToArrayTransformValues(): void
    {
        $ctx = new ExpressionContext([
            ExpressionTypeEnum::filter->value => 'cond',
            ExpressionTypeEnum::values->value => [':0' => 1, ':1' => 's'],
        ]);

        $out = $ctx->toArray(function (array $values): array {
            // Transform all values to strings prefixed 'x:'
            $mapped = [];
            foreach ($values as $k => $v) {
                $mapped[$k] = 'x:' . (is_scalar($v) ? (string)$v : json_encode($v));
            }
            return $mapped;
        });

        $this->assertSame('cond', $out[ExpressionTypeEnum::filter->value]);
        $this->assertSame([':0' => 'x:1', ':1' => 'x:s'], $out[ExpressionTypeEnum::values->value]);
    }

    public function testToArrayTransformMustReturnArray(): void
    {
        $ctx = new ExpressionContext([
            ExpressionTypeEnum::values->value => [':0' => 1],
        ]);

        $this->expectException(UnexpectedValueException::class);
        $ctx->toArray(fn(array $v) => 'not-an-array');
    }
}

