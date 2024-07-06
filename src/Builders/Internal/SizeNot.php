<?php

namespace DynaExp\Builders\Internal;

use Aws\DynamoDb\BinaryValue;
use DynaExp\Builders\Internal\Size;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Condition;

final class SizeNot
{
    public function __construct(private Size $size)
    {
    }

    /**
     * @param int|float|string|BinaryValue $lower
     * @param int|float|string|BinaryValue $upper
     * @return Condition
     */
    public function between(int|float|string|BinaryValue $lower, int|float|string|BinaryValue $upper): Condition
    {
        return new Condition($this->size->between($lower, $upper), ConditionTypeEnum::notCond);
    }

    /**
     * @param int|float|string|BinaryValue[] $range
     * @return Condition
     */
    public function in(int|float|string|BinaryValue ...$range): Condition
    {
        return new Condition($this->size->in($range), ConditionTypeEnum::notCond);
    } 
}
