<?php

namespace DynaExp\Nodes;

use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;
use function get_debug_type;
use function implode;
use function sprintf;

final readonly class Update implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    /**
     * @param array<int|string, ActionsSequence> $sequences
     */
    public function __construct(public array $sequences)
    {
        if ($sequences === []) {
            throw new InvalidArgumentException('Update requires at least one actions sequence.');
        }

        $seenActionTypes = [];

        foreach ($sequences as $sequence) {
            if (! $sequence instanceof ActionsSequence) {
                throw new InvalidArgumentException(sprintf(
                    'Update sequence must be %s, %s given.',
                    ActionsSequence::class,
                    get_debug_type($sequence),
                ));
            }

            $actionTypeName = $sequence->actionType->name;

            if (isset($seenActionTypes[$actionTypeName])) {
                throw new InvalidArgumentException(sprintf(
                    "Update action sequence '%s' must not be repeated.",
                    $sequence->actionType->value,
                ));
            }

            $seenActionTypes[$actionTypeName] = true;
        }
    }

    /**
     * @return array<int|string, ActionsSequence>
     */
    protected function operands(): array
    {
        return $this->sequences;
    }

    /**
     * @param array<int|string, string> $convertedNodes
     */
    protected function format(array $convertedNodes): string
    {
        return implode(' ', $convertedNodes);
    }
}
