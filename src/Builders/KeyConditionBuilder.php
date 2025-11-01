<?php

namespace DynaExp\Builders;

use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Nodes\KeyCondition;

final class KeyConditionBuilder
{
    /**
     * @var ?KeyCondition
     */
    private ?KeyCondition $rightKeyCondition;

    /**
     * @param KeyCondition $leftKeyCondition
     * @throws InvalidArgumentException
     */
    public function __construct(private KeyCondition $leftKeyCondition)
    {
        if ($leftKeyCondition->type === KeyConditionTypeEnum::andKeyCond) {

            throw new InvalidArgumentException("Condition 'AND' must not be used twice");

        }

        $this->rightKeyCondition = null;
    }

    /**
     * Creates a builder that ANDs the provided conditions.
     * 
     * @param KeyCondition $left
     * @param KeyCondition $right
     * @return self
     */
    public static function allOf(KeyCondition $left, KeyCondition $right): self
    {
        $builder = new self($left);
        $builder->and($right);
        return $builder;
    }

    /**
     * Adds a right-hand side KeyCondition with an AND operator to the existing left-hand side KeyCondition.
     * If a right-hand side KeyCondition has already been set, it will be overwritten.
     * This method is intended for a single AND combination scenario.
     *
     * @param KeyCondition $rightKeyCondition The right-hand side condition to be combined with the left one.
     * @return self
     * @throws InvalidArgumentException if the provided KeyCondition has a type of `AND`.
     */
    public function and(KeyCondition $rightKeyCondition): self
    {
        if ($rightKeyCondition->type === KeyConditionTypeEnum::andKeyCond) {

            throw new InvalidArgumentException("Condition 'AND' must not be used twice");

        }

        $this->rightKeyCondition = $rightKeyCondition;

        return $this;
    }

    /**
     * @return KeyCondition
     */
    public function build(): KeyCondition
    {
        return $this->rightKeyCondition ?
            new KeyCondition(KeyConditionTypeEnum::andKeyCond, $this->leftKeyCondition, $this->rightKeyCondition) :
            $this->leftKeyCondition;
    }
}
