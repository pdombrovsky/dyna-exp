<?php

namespace DynaExp\Builders;

use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Condition;
use UnexpectedValueException;

final class ConditionBuilder
{
    /**
     * @var Condition
     */
    private Condition $current;

    /**
     * @param Condition|callable(): Condition $condition
     */
    public function __construct(Condition|callable $condition)
    {
        $this->current = is_callable($condition) ? static::parenthesizeInnerCondition($condition) : $condition;
    }

    /**
     * @param Condition|callable(): Condition ...$conditions
     * @return ConditionBuilder
     */
    public function and(Condition|callable ...$conditions): ConditionBuilder
    {
        $this->glueConditions(ConditionTypeEnum::andCond, ...$conditions);

        return $this;
    }

    /**
     * @param Condition|callable(): Condition ...$conditions
     * @return ConditionBuilder
     */
    public function or(Condition|callable ...$conditions): ConditionBuilder
    {
        $this->glueConditions(ConditionTypeEnum::orCond, ...$conditions);

        return $this;
    }

    /**
     * @param ConditionTypeEnum $glue
     * @param Condition|callable(): Condition ...$conditions
     */
    private function glueConditions(ConditionTypeEnum $glue, Condition|callable ...$conditions): void
    {
        foreach ($conditions as $condition) {

            if (is_callable($condition)) {

                $condition = static::parenthesizeInnerCondition($condition);
            }

            $this->current = new Condition($glue, $this->current, $condition);
        }
    }
 
    /**
     * @param callable(): Condition $condition
     */
    private static function parenthesizeInnerCondition(callable $condition): Condition
    {
        $innerCondition = $condition();
        
        if (! $innerCondition instanceof Condition) {

            $type = gettype($innerCondition);

            if ($type === 'object' ) {

                $type = $innerCondition::class;
            }

            throw new UnexpectedValueException(sprintf("Callback returned an invalid result type: expected '%s', got: '%s'", Condition::class, $type));
        }

        return new Condition(ConditionTypeEnum::parenthesesCond, $innerCondition);
    }

    /**
     * @return Condition
     */
    public function build(): Condition
    {
        return $this->current;
    }
}
