<?php

namespace DynaExp\Builders;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Nodes\KeyCondition;

final class KeyConditionBuilder
{
    /**
     * @var KeyCondition
     */
    private KeyCondition $current;

    /**
     * @param KeyCondition $partitionKeyCondition
     * @throws InvalidArgumentException
     */
    public function __construct(KeyCondition $partitionKeyCondition)
    {
        if ($partitionKeyCondition->type !== KeyConditionTypeEnum::equalKeyCond) {

            throw new InvalidArgumentException("Equal key condition is allowed for primary key only");

        }

        $this->current = $partitionKeyCondition;
    }

    /**
     * @param KeyCondition $sortKeyCondition
     * @return void
     */
    public function and(KeyCondition $sortKeyCondition): void
    {
        if ($sortKeyCondition->type === KeyConditionTypeEnum::andKeyCond) {

            throw new InvalidArgumentException("Condition 'AND' must not be used twice");

        }

        $this->current = new KeyCondition(KeyConditionTypeEnum::andKeyCond, $this->current, $sortKeyCondition);
    }

    /**
     * @return KeyCondition
     */
    public function build(): KeyCondition
    {
        return $this->current;
    }
}
