<?php

namespace DynaExp\Tests\Nodes;

use DynaExp\Nodes\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathSearchExpressionTest extends TestCase
{
    public static function expressionsProvider(): array
    {
        return [
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
            // control characters are escaped as JSON string content
            [["line\nbreak"], false, '"line\nbreak"'],
            [["tab\tchar"], false, '"tab\tchar"'],
            [["carriage\rreturn"], false, '"carriage\rreturn"'],
            [["null\0byte"], false, '"null\u0000byte"'],
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

    /**
     * @param non-empty-list<string|int> $segments
     */
    #[DataProvider('expressionsProvider')]
    public function testSearchExpression(array $segments, bool $resetIndexes, string $expected): void
    {
        $node = Path::create(...$segments);
        $this->assertSame($expected, $node->searchExpression($resetIndexes));
    }

    public static function marshaledExpressionsProvider(): array
    {
        return [
            // top-level AttributeValue wrapper
            [['name'], false, '"name"'],
            // nested map/list path to AttributeValue wrapper
            [['profile', 'names', 2, 'first'], false, '"profile".M."names".L[2].M."first"'],
            // reset list indexes for shape/sample lookup
            [['profile', 'names', 2, 'first'], true, '"profile".M."names".L[0].M."first"'],
            // special attribute names are still quoted as JMESPath identifiers
            [['a.b', 'quote"key', 1, "line\nbreak"], false, '"a.b".M."quote\"key".L[1].M."line\nbreak"'],
            // top-level container wrappers stay at the top-level attribute
            [['settings'], false, '"settings"'],
            [['items'], false, '"items"'],
        ];
    }

    /**
     * @param non-empty-list<string|int> $segments
     */
    #[DataProvider('marshaledExpressionsProvider')]
    public function testMarshaledSearchExpression(
        array $segments,
        bool $resetIndexes,
        string $expected,
    ): void
    {
        $node = Path::create(...$segments);

        $this->assertSame($expected, $node->marshaledSearchExpression($resetIndexes));
    }
}
