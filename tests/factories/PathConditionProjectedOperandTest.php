<?php

namespace DynaExp\Tests\Factories;

use DynaExp\Evaluation\Evaluator;
use DynaExp\Factories\Path;
use PHPUnit\Framework\TestCase;

final class PathConditionProjectedOperandTest extends TestCase
{
    public function testProjectedPathCanBeUsedAsComparisonOperand(): void
    {
        $condition = Path::create('left')->equal(Path::create('right')->project());

        $evaluator = new Evaluator();

        $this->assertSame('#0 = #1', $evaluator->evaluate($condition));
        $this->assertSame(['#0' => 'left', '#1' => 'right'], $evaluator->getAttributeNameAliases());
        $this->assertSame([], $evaluator->getAttributeValueAliases());
    }

    public function testProjectedPathCanBeUsedAsContainsOperand(): void
    {
        $condition = Path::create('tags')->contains(Path::create('selectedTag')->project());

        $evaluator = new Evaluator();

        $this->assertSame('contains (#0, #1)', $evaluator->evaluate($condition));
        $this->assertSame(['#0' => 'tags', '#1' => 'selectedTag'], $evaluator->getAttributeNameAliases());
        $this->assertSame([], $evaluator->getAttributeValueAliases());
    }

    public function testPathFactoryObjectWithoutProjectIsTreatedAsValue(): void
    {
        $right = Path::create('right');
        $condition = Path::create('left')->equal($right);

        $evaluator = new Evaluator();

        $this->assertSame('#0 = :0', $evaluator->evaluate($condition));
        $this->assertSame(['#0' => 'left'], $evaluator->getAttributeNameAliases());
        $this->assertSame([':0' => $right], $evaluator->getAttributeValueAliases());
    }
}
