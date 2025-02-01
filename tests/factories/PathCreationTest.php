<?php

namespace DynaExp\Tests\Factories;

use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Factories\Path;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathCreationTest extends TestCase
{
    /**
     * @return array
     */
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

    /**
     * @param string $inputPath
     * @param string $expectedOutput
     * @return void
     */
    #[DataProvider('validPathsCreationProvider')]
    public function testValidPathsCreation(string $inputPath, string $expectedOutput)
    {
        $path = Path::fromString($inputPath);
        $this->assertSame($expectedOutput, $path->project()->__toString());
    }

    /**
     * @return array
     */
    public static function invalidPathsCreationProvider(): array
    {
        return [
            ['', 'Input string cannot be empty.', InvalidArgumentException::class],
            ['.attribute', "Empty attribute name found. Processed symbols: ''.", InvalidArgumentException::class],
            ['attribute[123[456]789]', "Nested brackets are not allowed. Processed symbols: 'attribute[123'.", InvalidArgumentException::class],
            ['attribute]', "Unmatched closing bracket. Processed symbols: 'attribute'.", InvalidArgumentException::class],
            ['listAttribute[]', "Empty index found. Processed symbols: 'listAttribute['.", InvalidArgumentException::class],
            ['listAttribute[1a]', "Only non-negative integers are allowed in index, '1a' given. Processed symbols: 'listAttribute[1a'.", InvalidArgumentException::class],
            ['attribute[1.0]', "Invalid character '.' inside brackets. Processed symbols: 'attribute[1'", InvalidArgumentException::class],
            ['listAttribute[-1]', "Only non-negative integers are allowed in index, '-1' given. Processed symbols: 'listAttribute[-1", InvalidArgumentException::class],
            ['listAttribute[1].next.attribute[1][2].br[oken', "Unmatched opening bracket. Processed symbols: 'listAttribute[1].next.attribute[1][2].br[oken'", InvalidArgumentException::class],
            ['[listAttribute', "Index used without a preceding attribute name. Processed symbols: ''.", InvalidArgumentException::class],
            ['list[0][5].Attribute.[2]', "Index used without a preceding attribute name. Processed symbols: 'list[0][5].Attribute.'.", InvalidArgumentException::class]
        ];
    }

    /**
     * @param string $inputPath
     * @param string $expectedMessage
     * @return void
     */
    #[DataProvider('invalidPathsCreationProvider')]
    public function testInvalidPathsCreation(string $inputPath, string $expectedMessage, string $exceptionClass)
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($expectedMessage);
        Path::fromString($inputPath);
    }
}
