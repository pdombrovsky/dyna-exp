<?php

namespace DynaExp\Nodes;

use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Nodes\Traits\NodesToStringTrait;
use Stringable;
use function sprintf;

final readonly class Operation implements EvaluableInterface, Stringable
{
    use NodesToStringTrait;

    private function __construct(
        public OperationTypeEnum $type,
        private EvaluableInterface $firstOperand,
        private mixed $secondOperand,
    ) {
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Operation
     */
    public static function plus(EvaluableInterface $left, mixed $right): self
    {
        return new self(OperationTypeEnum::plusValue, $left, $right);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Operation
     */
    public static function minus(EvaluableInterface $left, mixed $right): self
    {
        return new self(OperationTypeEnum::minusValue, $left, $right);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Operation
     */
    public static function listAppend(EvaluableInterface $left, mixed $right): self
    {
        return new self(OperationTypeEnum::listAppend, $left, $right);
    }

    /**
     * @param EvaluableInterface $left
     * @param mixed $right
     * @return Operation
     */
    public static function listPrepend(EvaluableInterface $left, mixed $right): self
    {
        return new self(OperationTypeEnum::listPrepend, $left, $right);
    }

    /**
     * @param Path $path
     * @param mixed $fallback
     * @return Operation
     */
    public static function ifNotExists(Path $path, mixed $fallback): self
    {
        return new self(OperationTypeEnum::ifNotExists, $path, $fallback);
    }

    /**
     * @return EvaluableInterface
     */
    public function firstOperand(): EvaluableInterface
    {
        return $this->firstOperand;
    }

    /**
     * @return mixed
     */
    public function secondOperand(): mixed
    {
        return $this->secondOperand;
    }

    /**
     * @return list<mixed>
     */
    protected function operands(): array
    {
        return [$this->firstOperand, $this->secondOperand];
    }

    /**
     * @param array<int|string, string> $convertedNodes
     */
    protected function format(array $convertedNodes): string
    {
        if (OperationTypeEnum::listPrepend == $this->type) {

            $convertedNodes = array_reverse($convertedNodes);

            $fmtString = OperationTypeEnum::listAppend->value;

        }
        else {
            
            $fmtString = $this->type->value;
        }

        return sprintf($fmtString, ...$convertedNodes);
    }
}
