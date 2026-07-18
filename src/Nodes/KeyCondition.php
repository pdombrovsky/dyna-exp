<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;
use function sprintf;

final readonly class KeyCondition implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    /**
     * @var list<mixed>
     */
    private array $tailOperands;

    /**
     * @param KeyConditionTypeEnum $type
     * @param EvaluableInterface $firstOperand
     * @param list<mixed> $tailOperands
     */
    private function __construct(
        public KeyConditionTypeEnum $type,
        private EvaluableInterface $firstOperand,
        array $tailOperands = [],
    )
    {
        $this->tailOperands = $tailOperands;
    }

    /**
     * @param Path $path
     * @param mixed $value
     * @return KeyCondition
     */
    public static function equal(Path $path, mixed $value): self
    {
        return new self(KeyConditionTypeEnum::equalKeyCond, $path, [$value]);
    }

    /**
     * @param Path $path
     * @param mixed $value
     * @return KeyCondition
     */
    public static function lessThan(Path $path, mixed $value): self
    {
        return new self(KeyConditionTypeEnum::lessThanKeyCond, $path, [$value]);
    }

    /**
     * @param Path $path
     * @param mixed $value
     * @return KeyCondition
     */
    public static function lessThanEqual(Path $path, mixed $value): self
    {
        return new self(KeyConditionTypeEnum::lessThanEqualKeyCond, $path, [$value]);
    }

    /**
     * @param Path $path
     * @param mixed $value
     * @return KeyCondition
     */
    public static function greaterThan(Path $path, mixed $value): self
    {
        return new self(KeyConditionTypeEnum::greaterThanKeyCond, $path, [$value]);
    }

    /**
     * @param Path $path
     * @param mixed $value
     * @return KeyCondition
     */
    public static function greaterThanEqual(Path $path, mixed $value): self
    {
        return new self(KeyConditionTypeEnum::greaterThanEqualKeyCond, $path, [$value]);
    }

    /**
     * @param Path $path
     * @param mixed $prefix
     * @return KeyCondition
     */
    public static function beginsWith(Path $path, mixed $prefix): self
    {
        return new self(KeyConditionTypeEnum::beginsWithKeyCond, $path, [$prefix]);
    }

    /**
     * @param Path $path
     * @param mixed $lower
     * @param mixed $upper
     * @return KeyCondition
     */
    public static function between(Path $path, mixed $lower, mixed $upper): self
    {
        return new self(KeyConditionTypeEnum::betweenKeyCond, $path, [$lower, $upper]);
    }

    public static function and(self $left, self $right): self
    {
        return new self(KeyConditionTypeEnum::andKeyCond, $left, [$right]);
    }

    /**
     * @return EvaluableInterface
     */
    public function firstOperand(): EvaluableInterface
    {
        return $this->firstOperand;
    }

    /**
     * @return list<mixed>
     */
    public function tailOperands(): array
    {
        return $this->tailOperands;
    }

    /**
     * @return list<mixed>
     */
    protected function operands(): array
    {
        return [$this->firstOperand, ...$this->tailOperands];
    }

    /**
     * @param array<int|string, string> $convertedNodes
     */
    protected function format(array $convertedNodes): string
    {
        return sprintf($this->type->value, ...$convertedNodes);
    }
}
