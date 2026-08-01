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
    private KeyCondition $keyCondition;

    /**
     * @param KeyCondition $keyCondition Initial key condition.
     * @throws InvalidArgumentException If the provided condition is an AND expression.
     */
    public function __construct(KeyCondition $keyCondition)
    {
        self::assertNotAndCondition($keyCondition);

        $this->keyCondition = $keyCondition;
    }

    /**
     * Creates a builder containing all provided key conditions joined by AND.
     *
     * @param KeyCondition $left  First key condition.
     * @param KeyCondition $right Second key condition.
     * @param KeyCondition ...$rest Additional key conditions.
     * @return self
     * @throws InvalidArgumentException If any provided condition is an AND expression.
     */
    public static function allOf(
        KeyCondition $left,
        KeyCondition $right,
        KeyCondition ...$rest
    ): self {
        $builder = new self($left);

        $builder->and($right);

        foreach ($rest as $keyCondition) {
            $builder->and($keyCondition);
        }

        return $builder;
    }

    /**
     * Adds a key condition joined to the current expression by AND.
     *
     * @param KeyCondition $keyCondition Key condition to add.
     * @return self
     * @throws InvalidArgumentException If the provided condition is an AND expression.
     */
    public function and(KeyCondition $keyCondition): self
    {
        self::assertNotAndCondition($keyCondition);

        $this->keyCondition = KeyCondition::and(
            $this->keyCondition,
            $keyCondition
        );

        return $this;
    }

    /**
     * Returns the resulting key condition expression.
     *
     * @return KeyCondition
     */
    public function build(): KeyCondition
    {
        return $this->keyCondition;
    }

    /**
     * Ensures that a combined AND condition is not passed to the builder.
     *
     * @param KeyCondition $keyCondition Key condition to validate.
     * @return void
     * @throws InvalidArgumentException If the provided condition is an AND expression.
     */
    private static function assertNotAndCondition(KeyCondition $keyCondition): void
    {
        if ($keyCondition->type === KeyConditionTypeEnum::andKeyCond) {
            throw new InvalidArgumentException(
                "Key condition of type 'AND' cannot be added to the builder."
            );
        }
    }
}

