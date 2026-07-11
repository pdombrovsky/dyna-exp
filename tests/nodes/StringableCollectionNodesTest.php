<?php

namespace DynaExp\Tests\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\PathNode;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Update;
use PHPUnit\Framework\TestCase;

final class StringableCollectionNodesTest extends TestCase
{
    public function testProjectionIsStringable(): void
    {
        $projection = new Projection([
            PathNode::create('profile', 'name'),
            PathNode::create('orders', 0, 'total'),
        ]);

        $this->assertSame('profile.name, orders[0].total', (string) $projection);
    }

    public function testActionsSequenceIsStringable(): void
    {
        $sequence = new ActionsSequence(ActionTypeEnum::set, [
            Action::set(PathNode::create('name'), 'Bob'),
            Action::set(PathNode::create('count'), 1),
        ]);

        $this->assertSame('SET name = Bob, count = 1', (string) $sequence);
    }

    public function testUpdateIsStringable(): void
    {
        $update = new Update([
            new ActionsSequence(ActionTypeEnum::set, [
                Action::set(PathNode::create('name'), 'Bob'),
            ]),
            new ActionsSequence(ActionTypeEnum::remove, [
                Action::remove(PathNode::create('oldName')),
            ]),
        ]);

        $this->assertSame('SET name = Bob REMOVE oldName', (string) $update);
    }
}
