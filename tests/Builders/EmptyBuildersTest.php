<?php

namespace DynaExp\Tests\Builders;

use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Exceptions\RuntimeException;
use PHPUnit\Framework\TestCase;

final class EmptyBuildersTest extends TestCase
{
    public function testProjectionBuilderRequiresAtLeastOnePath(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Projection requires at least one attribute.');

        (new ProjectionBuilder())->build();
    }

    public function testUpdateBuilderRequiresAtLeastOneAction(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Update requires at least one actions sequence.');

        (new UpdateBuilder())->build();
    }
}
