<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;
use function get_debug_type;
use function sprintf;

final readonly class ActionsSequence implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    /**
     * @param ActionTypeEnum $actionType
     * @param array<int|string, Action> $actions
     */
    public function __construct(public ActionTypeEnum $actionType, public array $actions)
    {
        if ($actions === []) {
            throw new InvalidArgumentException('Actions sequence requires at least one action.');
        }

        foreach ($actions as $action) {
            if (! $action instanceof Action) {
                throw new InvalidArgumentException(sprintf(
                    'Actions sequence item must be %s, %s given.',
                    Action::class,
                    get_debug_type($action),
                ));
            }

            if ($action->type !== $actionType) {
                throw new InvalidArgumentException(sprintf(
                    "Actions sequence type '%s' does not match action type '%s'.",
                    $actionType->value,
                    $action->type->value,
                ));
            }
        }
    }

    /**
     * @return array<int|string, Action>
     */
    protected function operands(): array
    {
        return $this->actions;
    }

    /**
     * @param array<int|string, string> $convertedNodes
     */
    protected function format(array $convertedNodes): string
    {
        return $this->actionType->value . ' ' . implode(', ', $convertedNodes);
    }
}
