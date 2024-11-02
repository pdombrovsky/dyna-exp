<?php

use DynaExp\Evaluation\Aliases\Values;
use PHPUnit\Framework\TestCase;

final class ValuesTest extends TestCase
{
    public function testAliasValues()
    {
        $someObject = new stdClass();

        $parts = ['abc', 1, $someObject, [1, 2, 3], 'val',  [1, 2, 3]];

        $values = new Values();

        foreach ($parts as $part) {

            $values->alias($part);
        }

        $this->assertSame(
            [
                ':0' => 'abc',
                ':1' => 1,
                ':2' => $someObject,
                ':3' => [1, 2, 3],
                ':4' => 'val',
                ':5' => [1, 2, 3],
            ],
            $values->getMap(),
        );
    }
}
