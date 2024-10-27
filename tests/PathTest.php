<?php

use DynaExp\Factories\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathTest extends TestCase
{
    public static function validPathsProvider(): array
    {
        return [
            ['attribute', 'attribute'],
            ['attribute1.attribute2.attribute3', 'attribute1.attribute2.attribute3'],
            ['listAttribute[0]', 'listAttribute[0]'],
            ['attribute1.attribute2[0].attribute3', 'attribute1.attribute2[0].attribute3'],
            ['listAttribute[10][20]', 'listAttribute[10][20]']
        ];
    }

    #[DataProvider('validPathsProvider')]
    public function testValidPaths(string $inputPath, string $expectedOutput)
    {
        $path = Path::fromString($inputPath);
        $this->assertSame($expectedOutput, $path->pathNode->__toString());
    }

    public static function invalidPathsProvider(): array
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


    #[DataProvider('invalidPathsProvider')]
    public function testInvalidPaths(string $inputPath, string $expectedMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        Path::fromString($inputPath);
    }
}
