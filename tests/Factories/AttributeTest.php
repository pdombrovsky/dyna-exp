<?php

namespace DynaExp\Tests\Factories;

use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Factories\Attribute;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AttributeTest extends TestCase
{
    /**
     * @return array
     */
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

    /**
     * @param string $attribue
     * @param array $segments
     * @param string $expectedOutput
     * @return void
     */
    #[DataProvider('validPathsProvider')]
    public function testValidPaths(string $attribue, array $segments, string $expectedOutput)
    {
        $path = Attribute::create($attribue, ...$segments);
        $this->assertSame($expectedOutput, $path->__toString());
    }

    public function testProjectedPathIsCountable(): void
    {
        $node = Attribute::create('map', 'nestedAttribute1', 10, 'nestedAttribute2', 20)->project();

        $this->assertCount(5, $node);
    }

    /**
     * @return array
     */
    public static function invalidPathsProvider(): array
    {
        return [
            ['', [], "Wrong path segment found after: ''. Path segment can not be empty string."],
            ['attribute', ['nested', -1], "Wrong path segment found after: 'attribute.nested'. Index can not be negative, '-1' given."],
            ['attribute', ['nested', 1, ''], "Wrong path segment found after: 'attribute.nested[1]'. Path segment can not be empty string."],
        ];
    }

    /**
     * @param string $attribue
     * @param array $segments
     * @param string $expectedMessage
     * @return void
     */
    #[DataProvider('invalidPathsProvider')]
    public function testInvalidPaths(string $attribue, array $segments, string $expectedMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        Attribute::create($attribue, ...$segments);
    }
}
