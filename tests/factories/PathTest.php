<?php

use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Factories\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathTest extends TestCase
{
    public static function validPathsCreationProvider(): array
    {
        return [
            ['attribute', 'attribute'],
            ['attribute1.attribute2.attribute3', 'attribute1.attribute2.attribute3'],
            ['listAttribute[0]', 'listAttribute[0]'],
            ['attribute1.attribute2[0].attribute3', 'attribute1.attribute2[0].attribute3'],
            ['listAttribute[10][20]', 'listAttribute[10][20]']
        ];
    }

    #[DataProvider('validPathsCreationProvider')]
    public function testValidPathsCreation(string $inputPath, string $expectedOutput)
    {
        $path = Path::fromString($inputPath);
        $this->assertSame($expectedOutput, $path->projection()->__toString());
    }

    public static function validPathsProvider(): array
    {
        return [
            ['attribute', [], 'attribute'],
            ['attribute1', ['attribute2', 'attribute3'], 'attribute1.attribute2.attribute3'],
            ['listAttribute', [0], 'listAttribute[0]'],
            ['attribute1', ['attribute2', 0, 'attribute3'],'attribute1.attribute2[0].attribute3'],
            ['listAttribute', [10, 20], 'listAttribute[10][20]'],
            ['map', ['nestedAttribute1', 10, 'nestedAttribute2', 20], 'map.nestedAttribute1[10].nestedAttribute2[20]']
        ];
    }

    #[DataProvider('validPathsProvider')]
    public function testValidPaths(string $attribue, array $segments, string $expectedOutput)
    {
        $path = new Path($attribue, ...$segments);
        $this->assertSame($expectedOutput, $path->projection()->__toString());
    }

    public static function invalidPathsCreationProvider(): array
    {
        return [
            ['', 'Input string cannot be empty.'],
            ['.attribute', "Empty attribute name found. Processed symbols: ''."],
            ['attribute[123[456]789]', "Nested brackets are not allowed. Processed symbols: 'attribute[123'."],
            ['attribute]', "Unmatched closing bracket. Processed symbols: 'attribute'."],
            ['listAttribute[]', "Empty index found. Processed symbols: 'listAttribute['."],
            ['listAttribute[1a]', "Only non-negative integers are allowed in index, '1a' given. Processed symbols: 'listAttribute[1a'."],
            ['attribute[1.0]', "Invalid character '.' inside brackets. Processed symbols: 'attribute[1'"],
            ['listAttribute[-1]', "Only non-negative integers are allowed in index, '-1' given. Processed symbols: 'listAttribute[-1"],
            ['listAttribute[1].next.attribute[1][2].br[oken', "Unmatched opening bracket. Processed symbols: 'listAttribute[1].next.attribute[1][2].br[oken'"],
            ['[listAttribute', "Index used without a preceding attribute name. Processed symbols: ''."],
            ['list[0][5].Attribute.[2]', "Index used without a preceding attribute name. Processed symbols: 'list[0][5].Attribute.'."]
        ];
    }


    #[DataProvider('invalidPathsCreationProvider')]
    public function testInvalidPathsCreation(string $inputPath, string $expectedMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        Path::fromString($inputPath);
    }

    public static function invalidPathsProvider(): array
    {
        return [
            ['', [], 'Attribute can not be empty string.'],
            ['attribute', ['nested', -1], "Wrong path segment found after: 'attribute.nested'. Index can not be negative, '-1' given."],
            ['attribute', ['nested', 1, ''], "Wrong path segment found after: 'attribute.nested[1]'. Path segment can not be empty string."],
        ];
    }

    #[DataProvider('invalidPathsProvider')]
    public function testInvalidPaths(string $attribue, array $segments, string $expectedMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        new Path($attribue, ...$segments);
    }
}
