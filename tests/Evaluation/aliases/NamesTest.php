<?php

namespace DynaExp\Tests\Evaluation\Aliases;

use DynaExp\Evaluation\Aliases\Names;
use PHPUnit\Framework\TestCase;

final class NamesTest extends TestCase
{
    public function testAliasNames()
    {
        $parts = ['abc', 'bce', 'def', 'jhk', 'lon', 'lom'];

        $parts = [...$parts, ...$parts, ...$parts];

        $names = new Names();

        foreach ($parts as $part) {

            $names->alias($part);
        }

        $this->assertSame(
            [
                '#0' => 'abc',
                '#1' => 'bce',
                '#2' => 'def',
                '#3' => 'jhk',
                '#4' => 'lon',
                '#5' => 'lom',
            ],
            $names->getMap(),
        );
    }
}
