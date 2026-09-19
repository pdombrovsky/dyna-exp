<?php

namespace DynaExp\Tests\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\Path;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Update;
use PHPUnit\Framework\TestCase;

final class StringableCollectionNodesTest extends TestCase
{
    public function testProjectionIsStringable(): void
    {
        $projection = new Projection([
            Path::create('profile', 'name'),
            Path::create('orders', 0, 'total'),
        ]);

        $this->assertSame('profile.name, orders[0].total', (string) $projection);
    }

    public function testActionsSequenceIsStringable(): void
    {
        $sequence = new ActionsSequence(ActionTypeEnum::set, [
            Action::set(Path::create('name'), 'Bob'),
            Action::set(Path::create('count'), 1),
        ]);

        $this->assertSame('SET name = Bob, count = 1', (string) $sequence);
    }

    public function testUpdateIsStringable(): void
    {
        $update = new Update([
            new ActionsSequence(ActionTypeEnum::set, [
                Action::set(Path::create('name'), 'Bob'),
            ]),
            new ActionsSequence(ActionTypeEnum::remove, [
                Action::remove(Path::create('oldName')),
            ]),
        ]);

        $this->assertSame('SET name = Bob REMOVE oldName', (string) $update);
    }
}
