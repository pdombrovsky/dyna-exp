<?php

namespace DynaExp\Tests\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\Path;
use DynaExp\Nodes\Projection;
use DynaExp\Nodes\Update;
use PHPUnit\Framework\TestCase;

final class CollectionNodeValidationTest extends TestCase
{
    public function testProjectionRequiresAtLeastOnePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Projection requires at least one attribute.');

        new Projection([]);
    }

    public function testProjectionRequiresPaths(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Projection attribute must be DynaExp\Nodes\Path, string given.');

        new Projection(['name']);
    }

    public function testActionsSequenceRequiresAtLeastOneAction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Actions sequence requires at least one action.');

        new ActionsSequence(ActionTypeEnum::set, []);
    }

    public function testActionsSequenceRequiresActions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Actions sequence item must be DynaExp\Nodes\Action, string given.');

        new ActionsSequence(ActionTypeEnum::set, ['name']);
    }

    public function testActionsSequenceRequiresMatchingActionType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Actions sequence type 'SET' does not match action type 'REMOVE'.");

        new ActionsSequence(ActionTypeEnum::set, [
            Action::remove(Path::create('oldName')),
        ]);
    }

    public function testUpdateRequiresAtLeastOneActionSequence(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Update requires at least one actions sequence.');

        new Update([]);
    }

    public function testUpdateRequiresActionSequences(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Update sequence must be DynaExp\Nodes\ActionsSequence, string given.');

        new Update(['SET #0 = :0']);
    }

    public function testUpdateRejectsDuplicateActionSequenceTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Update action sequence 'SET' must not be repeated.");

        new Update([
            new ActionsSequence(ActionTypeEnum::set, [
                Action::set(Path::create('name'), 'Bob'),
            ]),
            new ActionsSequence(ActionTypeEnum::set, [
                Action::set(Path::create('count'), 1),
            ]),
        ]);
    }
}
