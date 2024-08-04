<?php

namespace DynaExp\Builders;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Interfaces\BuilderInterface;
use DynaExp\Interfaces\EvaluableInterface;
use DynaExp\Nodes\KeyCondition;

use RuntimeException;

final class KeyConditionBuilder implements BuilderInterface
{
    /**
     * @var EvaluableInterface
     */
    private EvaluableInterface $current;

    /**
     * @param KeyCondition $primaryKeyCondition
     * @throws RuntimeException
     */
    public function __construct(KeyCondition $primaryKeyCondition)
    {
        if ($primaryKeyCondition->type !== KeyConditionTypeEnum::equalKeyCond) {

            throw new RuntimeException("Equal key condition is allowed for primary key only");

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

            throw new RuntimeException("Condition 'AND' must not be used twice");

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
