<?php

namespace DynaExp\Builders;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Nodes\KeyCondition;

use InvalidArgumentException;

final class KeyConditionBuilder
{
    /**
     * @var KeyCondition
     */
    private KeyCondition $current;

    /**
     * @param KeyCondition $primaryKeyCondition
     * @throws InvalidArgumentException
     */
    public function __construct(KeyCondition $primaryKeyCondition)
    {
        if ($primaryKeyCondition->type !== KeyConditionTypeEnum::equalKeyCond) {

            throw new InvalidArgumentException("Equal key condition is allowed for primary key only");

        }

        $this->current = $primaryKeyCondition;
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

        $this->current = new KeyCondition($this->current, KeyConditionTypeEnum::andKeyCond, $sortKeyCondition);
    }

    /**
     * @return KeyCondition
     */
    public function build(): KeyCondition
    {
        return $this->current;
    }
}
