<?php

namespace DynaExp\Tests\Factories;

use DynaExp\Evaluation\Evaluator;
use DynaExp\Factories\Path;
use PHPUnit\Framework\TestCase;

final class PathDeepHierarchyTest extends TestCase
{
    public function testEvaluatesDeepHierarchyWithManySegments(): void
    {
        $path = Path::create(
            'level0',
            'level1',
            'level2',
            'level3',
            0,
            'level4',
            1,
            'level5',
            'level6',
            'level7',
            'level8',
            'level9'
        );

        $node = $path->project();
        $evaluator = new Evaluator();

        $evaluated = $node->evaluate($evaluator);

        $this->assertSame('#0.#1.#2.#3[0].#4[1].#5.#6.#7.#8.#9', $evaluated);

        $this->assertSame(
            [
                '#0' => 'level0',
                '#1' => 'level1',
                '#2' => 'level2',
                '#3' => 'level3',
                '#4' => 'level4',
                '#5' => 'level5',
                '#6' => 'level6',
                '#7' => 'level7',
                '#8' => 'level8',
                '#9' => 'level9',
            ],
            $evaluator->getAttributeNameAliases()
        );
    }
}
