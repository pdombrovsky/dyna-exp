<?php

namespace DynaExp\Tests\Nodes;

use DynaExp\Nodes\PathNode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathNodeSearchExpressionTest extends TestCase
{
    public static function expressionsProvider(): array
    {
        return [
            // empty path
            [[], false, ''],
            // simple attributes
            [['a'], false, '"a"'],
            [['a', 'b'], false, '"a"."b"'],
            // indexes
            [['a', 2, 'b'], false, '"a"[2]."b"'],
            [['a', 5, 'b'], true, '"a"[0]."b"'],
            // attribute with dot inside
            [['a.b', 'c'], false, '"a.b"."c"'],
            // attribute with a double quote inside
            [['a"b'], false, '"a\"b"'],
            // attribute with backslash inside
            [['a\\b'], false, '"a\\\\b"'],
            // multiple indexes and attributes
            [['a', 0, 'b', 10, 'c'], false, '"a"[0]."b"[10]."c"'],
            // reset indexes across multiple
            [['a', 3, 'b', 4], true, '"a"[0]."b"[0]'],
            // with spaces
            [['key', 'key with space'], false, '"key"."key with space"'],
            // complex mix
            [['a', 1, 'b.c', 2, 'd"e', 3], false, '"a"[1]."b.c"[2]."d\"e"[3]'],
        ];
    }

    #[DataProvider('expressionsProvider')]
    public function testSearchExpression(array $segments, bool $resetIndexes, string $expected): void
    {
        $node = new PathNode($segments);
        $this->assertSame($expected, $node->searchExpression($resetIndexes));
    }
}
