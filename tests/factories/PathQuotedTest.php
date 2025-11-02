<?php

namespace DynaExp\Tests\Factories;

use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Factories\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathQuotedTest extends TestCase
{
    public static function validQuotedPathsProvider(): array
    {
        return [
            ['"a.b".c', 'a.b.c'],
            ['"a".b', 'a.b'],
            ['a."b.c"[10]."d\"e".f', 'a.b.c[10].d"e.f'],
            ['"root"."child.with.dots"', 'root.child.with.dots'],
            ['prefix."with\\backslash".suffix', 'prefix.with\\backslash.suffix'],
            ['testMap."attr\"with\"double\"quotes"[0]', 'testMap.attr"with"double"quotes[0]'],
            ['"a.b"[0].c', 'a.b[0].c'],
            ['list[0]."a.b"', 'list[0].a.b'],
            ['"with space".x', 'with space.x'],
            ['"a/b".c', 'a/b.c'],
            ['"attr"[0]', 'attr[0]'],
        ];
    }

    #[DataProvider('validQuotedPathsProvider')]
    public function testValidQuotedPaths(string $input, string $expected)
    {
        $path = Path::fromString($input);
        $this->assertSame($expected, (string) $path);
    }

    public static function invalidQuotedPathsProvider(): array
    {
        return [
            ['attr["x"]', "Quoted attribute name is not allowed inside brackets. Processed symbols: 'attr['.", InvalidArgumentException::class],
            ['attr."x', "Unmatched quote. Processed symbols: 'attr.\"x'.", InvalidArgumentException::class],
            ['a.b"c".d', "Unexpected '\"' inside attribute name. Processed symbols: 'a.b'.", InvalidArgumentException::class],
            ['a."b\\"c', "Unmatched quote. Processed symbols: 'a.\"b\\\"c'.", InvalidArgumentException::class],
            ['map["a.b"]', "Quoted attribute name is not allowed inside brackets. Processed symbols: 'map['.", InvalidArgumentException::class],
            ['"a.b"[x]', "Only non-negative integers are allowed in index, 'x' given. Processed symbols: '\"a.b\"[x'.", InvalidArgumentException::class],
            ['ab"cd".e', "Unexpected '\"' inside attribute name. Processed symbols: 'ab'.", InvalidArgumentException::class],
            ['"a.b".', "Empty attribute name found. Processed symbols: '\"a.b\"'.", InvalidArgumentException::class],
            ['list[0]"a"', "Quoted attribute must start at beginning or after a dot. Processed symbols: 'list[0]'.", InvalidArgumentException::class],
            ['"a.b"..c', "Empty attribute name found. Processed symbols: '\"a.b\".'.", InvalidArgumentException::class],
            ['a..\"b\"', "Empty attribute name found. Processed symbols: 'a.'.", InvalidArgumentException::class],
            ['list[0].', "Empty attribute name found. Processed symbols: 'list[0]'.", InvalidArgumentException::class],
        ];
    }

    #[DataProvider('invalidQuotedPathsProvider')]
    public function testInvalidQuotedPaths(string $input, string $message, string $exception)
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);
        Path::fromString($input);
    }
}
