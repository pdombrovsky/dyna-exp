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
        self::assertSimple($leftKeyCondition);

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
     * Adds a single right-hand side KeyCondition with an AND operator.
     *
     * @param KeyCondition $rightKeyCondition The right-hand side condition to be combined with the left one.
     * @return self
     * @throws InvalidArgumentException
     */
    public function and(KeyCondition $rightKeyCondition): self
    {
        if ($this->rightKeyCondition !== null) {

            throw new InvalidArgumentException('Only one AND key condition is allowed.');

        }

        self::assertSimple($rightKeyCondition);

        $this->rightKeyCondition = $rightKeyCondition;

        return $this;
    }

    /**
     * @return KeyCondition
     */
    public function build(): KeyCondition
    {
        return $this->rightKeyCondition ?
            KeyCondition::and($this->leftKeyCondition, $this->rightKeyCondition) :
            $this->leftKeyCondition;
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function assertSimple(KeyCondition $condition): void
    {
        if ($condition->type === KeyConditionTypeEnum::andKeyCond) {

            throw new InvalidArgumentException("Condition 'AND' must not be nested.");

        }
    }

}
