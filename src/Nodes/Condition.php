<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;
use function sprintf;

final readonly class Condition implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    /**
     * @var list<mixed>
     */
    private array $tailOperands;

    /**
     * @param ConditionTypeEnum $type
     * @param EvaluableInterface $firstOperand
     * @param list<mixed> $tailOperands
     */
    private function __construct(
        public ConditionTypeEnum $type,
        private EvaluableInterface $firstOperand,
        array $tailOperands = [],
    )
    {
        $this->tailOperands = $tailOperands;
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Condition
     */
    public static function equal(EvaluableInterface $left, mixed $right): self
    {
        return new self(ConditionTypeEnum::equalCond, $left, [$right]);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Condition
     */
    public static function notEqual(EvaluableInterface $left, mixed $right): self
    {
        return new self(ConditionTypeEnum::notEqualCond, $left, [$right]);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Condition
     */
    public static function lessThan(EvaluableInterface $left, mixed $right): self
    {
        return new self(ConditionTypeEnum::lessThanCond, $left, [$right]);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Condition
     */
    public static function lessThanEqual(EvaluableInterface $left, mixed $right): self
    {
        return new self(ConditionTypeEnum::lessThanEqualCond, $left, [$right]);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Condition
     */
    public static function greaterThan(EvaluableInterface $left, mixed $right): self
    {
        return new self(ConditionTypeEnum::greaterThanCond, $left, [$right]);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Condition
     */
    public static function greaterThanEqual(EvaluableInterface $left, mixed $right): self
    {
        return new self(ConditionTypeEnum::greaterThanEqualCond, $left, [$right]);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $lower
     * @param mixed $upper
     * @return Condition
     */
    public static function between(EvaluableInterface $left, mixed $lower, mixed $upper): self
    {
        return new self(ConditionTypeEnum::betweenCond, $left, [$lower, $upper]);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $value
     * @param mixed ...$rest
     * @return Condition
     */
    public static function in(EvaluableInterface $left, mixed $value, mixed ...$rest): self
    {
        /** @var list<mixed> $tailOperands */
        $tailOperands = [$value, ...$rest];

        return new self(ConditionTypeEnum::inCond, $left, $tailOperands);
    }

    /**
     * @param Path $target
     * @param string $type
     * @return Condition
     */
    public static function attributeType(Path $target, string $type): self
    {
        return new self(ConditionTypeEnum::attrTypeCond, $target, [$type]);
    }

    /**
     * @param Path $target
     * @param mixed $prefix
     * @return Condition
     */
    public static function beginsWith(Path $target, mixed $prefix): self
    {
        return new self(ConditionTypeEnum::beginsWithCond, $target, [$prefix]);
    }

    /**
     * @param Path $target
     * @param mixed $value
     * @return Condition
     */
    public static function contains(Path $target, mixed $value): self
    {
        return new self(ConditionTypeEnum::containsCond, $target, [$value]);
    }

    /**
     * @param Path $target
     * @return Condition
     */
    public static function attributeExists(Path $target): self
    {
        return new self(ConditionTypeEnum::attrExistsCond, $target);
    }

    /**
     * @param Path $target
     * @return Condition
     */
    public static function attributeNotExists(Path $target): self
    {
        return new self(ConditionTypeEnum::attrNotExistsCond, $target);
    }

    /**
     * @param Condition $inner
     * @return Condition
     */
    public static function not(self $inner): self
    {
        return new self(ConditionTypeEnum::notCond, $inner);
    }

    /**
     * @param Condition $left
     * @param Condition $right
     * @return Condition
     */
    public static function and(self $left, self $right): self
    {
        return new self(ConditionTypeEnum::andCond, $left, [$right]);
    }

    /**
     * @param Condition $left
     * @param Condition $right
     * @return Condition
     */
    public static function or(self $left, self $right): self
    {
        return new self(ConditionTypeEnum::orCond, $left, [$right]);
    }

    /**
     * @param Condition $inner
     * @return Condition
     */
    public static function parenthesized(self $inner): self
    {
        return new self(ConditionTypeEnum::parenthesesCond, $inner);
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
        if ($this->type === ConditionTypeEnum::inCond) {

            $fmtString = str_replace('(%s)', '(' . str_repeat('%s, ', count($convertedNodes) - 2) . '%s)', $this->type->value);
        }
        else {

            $fmtString = $this->type->value;
        }
 
        return sprintf($fmtString, ...$convertedNodes);
    }
}
