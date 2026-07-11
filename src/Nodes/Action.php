<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;
use function sprintf;

final readonly class Action implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    /**
     * @param ActionTypeEnum $type
     * @param PathNode $target
     * @param mixed $argument
     */
    private function __construct(
        public ActionTypeEnum $type,
        private PathNode $target,
        private mixed $argument = null,
    ) {
    }

    /**
     * @param PathNode $target
     * @param mixed $value
     * @return Action
     */
    public static function set(PathNode $target, mixed $value): self
    {
        return new self(ActionTypeEnum::set, $target, $value);
    }

    /**
     * @param PathNode $target
     * @param mixed $value
     * @return Action
     */
    public static function add(PathNode $target, mixed $value): self
    {
        return new self(ActionTypeEnum::add, $target, $value);
    }

    /**
     * @param PathNode $target
     * @param mixed $value
     * @return Action
     */
    public static function delete(PathNode $target, mixed $value): self
    {
        return new self(ActionTypeEnum::delete, $target, $value);
    }

    /**
     * @param PathNode $target
     * @return Action
     */
    public static function remove(PathNode $target): self
    {
        return new self(ActionTypeEnum::remove, $target);
    }

    /**
     * @return PathNode
     */
    public function target(): PathNode
    {
        return $this->target;
    }

    /**
     * @return mixed
     */
    public function argument(): mixed
    {
        return $this->argument;
    }

    /**
     * @return list<mixed>
     */
    protected function operands(): array
    {
        return $this->type === ActionTypeEnum::remove
            ? [$this->target]
            : [$this->target, $this->argument];
    }

    /**
     * @param array<int|string, string> $convertedNodes
     */
    protected function format(array $convertedNodes): string
    {
        return sprintf($this->type->fmtString(), ...$convertedNodes);
    }
}
