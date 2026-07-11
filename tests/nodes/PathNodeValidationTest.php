<?php

namespace DynaExp\Tests\Nodes;

use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Nodes\PathNode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

final class PathNodeValidationTest extends TestCase
{
    public static function invalidChildSegmentsProvider(): array
    {
        return [
            [
                [false],
                "Wrong path segment found after: ''. Path segment must be string or int, bool given.",
            ],
            [
                [0, false],
                "Wrong path segment found after: '[0]'. Path segment must be string or int, bool given.",
            ],
            [
                [1.5],
                "Wrong path segment found after: ''. Path segment must be string or int, float given.",
            ],
            [
                [new stdClass()],
                "Wrong path segment found after: ''. Path segment must be string or int, stdClass given.",
            ],
        ];
    }

    #[DataProvider('invalidChildSegmentsProvider')]
    public function testChildRejectsUnsupportedSegmentTypes(array $segments, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        PathNode::create('root')->child($segments);
    }
}
