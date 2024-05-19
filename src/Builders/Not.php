<?php

namespace DynaExp\Builders;

use Aws\DynamoDb\BinaryValue;
use DynaExp\Builders\Name;
use DynaExp\Enums\AttributeTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Condition;


readonly class Not
{
    public function __construct(private Name $name)
    {
    }

    /**
     * @param AttributeTypeEnum $type
     * @return Condition
     */
    public function attributeType(AttributeTypeEnum $type): Condition
    {
        return new Condition($this->name->attributeType($type), ConditionTypeEnum::notCond);
    }

    /**
     * @param int|float|string|BinaryValue $prefix
     * @return Condition
     */
    public function beginsWith(int|float|string|BinaryValue $prefix): Condition
    {
        return new Condition($this->name->beginsWith($prefix), ConditionTypeEnum::notCond);
    }

    /**
     * @param int|float|string|BinaryValue $contains
     * @return Condition
     */
    public function contains(int|float|string|BinaryValue $contains): Condition
    {
        return new Condition($this->name->contains($contains), ConditionTypeEnum::notCond);
    }

    /**
     * @param int|float|string|BinaryValue $lower
     * @param int|float|string|BinaryValue $upper
     * @return Condition
     */
    public function between(int|float|string|BinaryValue $lower, int|float|string|BinaryValue $upper): Condition
    {
        return new Condition($this->name->between($lower, $upper), ConditionTypeEnum::notCond);
    }

    /**
     * @param int|float|string|BinaryValue[] $range
     * @return Condition
     */
    public function in(int|float|string|BinaryValue ...$range): Condition
    {
        return new Condition($this->name->in($range), ConditionTypeEnum::notCond);
    } 
}
