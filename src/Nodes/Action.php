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
     * @param Path $target
     * @param mixed $argument
     */
    private function __construct(
        public ActionTypeEnum $type,
        private Path $target,
        private mixed $argument = null,
    ) {
    }

    /**
     * @param Path $target
     * @param mixed $value
     * @return Action
     */
    public static function set(Path $target, mixed $value): self
    {
        return new self(ActionTypeEnum::set, $target, $value);
    }

    /**
     * @param Path $target
     * @param mixed $value
     * @return Action
     */
    public static function add(Path $target, mixed $value): self
    {
        return new self(ActionTypeEnum::add, $target, $value);
    }

    /**
     * @param Path $target
     * @param mixed $value
     * @return Action
     */
    public static function delete(Path $target, mixed $value): self
    {
        return new self(ActionTypeEnum::delete, $target, $value);
    }

    /**
     * @param Path $target
     * @return Action
     */
    public static function remove(Path $target): self
    {
        return new self(ActionTypeEnum::remove, $target);
    }

    /**
     * @return Path
     */
    public function target(): Path
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
