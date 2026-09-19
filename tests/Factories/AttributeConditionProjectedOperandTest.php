<?php

namespace DynaExp\Tests\Factories;

use DynaExp\Evaluation\Evaluator;
use DynaExp\Factories\Attribute;
use PHPUnit\Framework\TestCase;

final class AttributeConditionProjectedOperandTest extends TestCase
{
    public function testProjectedPathCanBeUsedAsComparisonOperand(): void
    {
        $condition = Attribute::create('left')->equal(Attribute::create('right')->project());

        $evaluator = new Evaluator();

        $this->assertSame('#0 = #1', $evaluator->evaluate($condition));
        $this->assertSame(['#0' => 'left', '#1' => 'right'], $evaluator->getAttributeNameAliases());
        $this->assertSame([], $evaluator->getAttributeValueAliases());
    }

    public function testProjectedPathCanBeUsedAsContainsOperand(): void
    {
        $condition = Attribute::create('tags')->contains(Attribute::create('selectedTag')->project());

        $evaluator = new Evaluator();

        $this->assertSame('contains (#0, #1)', $evaluator->evaluate($condition));
        $this->assertSame(['#0' => 'tags', '#1' => 'selectedTag'], $evaluator->getAttributeNameAliases());
        $this->assertSame([], $evaluator->getAttributeValueAliases());
    }

    public function testPathFactoryObjectWithoutProjectIsTreatedAsValue(): void
    {
        $right = Attribute::create('right');
        $condition = Attribute::create('left')->equal($right);

        $evaluator = new Evaluator();

        $this->assertSame('#0 = :0', $evaluator->evaluate($condition));
        $this->assertSame(['#0' => 'left'], $evaluator->getAttributeNameAliases());
        $this->assertSame([':0' => $right], $evaluator->getAttributeValueAliases());
    }
}
