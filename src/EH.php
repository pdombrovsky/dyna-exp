<?php

namespace DynaExp;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\Key;
use DynaExp\Builders\KeyConditionBuilder;
use DynaExp\Builders\Name;
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\KeyCondition;
use RuntimeException;

class EH
{
    /**
     * @param string $attributeName
     * @return Name
     */
    public static function name(string $attributeName): Name
    {
        return new Name($attributeName);
    }

    /**
     * @param string $attributeName
     * @param string|int ...$pathParts
     * @return Name
     */
    public static function nameFromParts(string $attributeName, string|int ...$pathParts): Name
    {
        $name = new Name($attributeName);

        foreach ($pathParts as $part) {

            if (is_int($part)) {

                $name->index($part);

                continue;
            }

            $name->path($part);

        }

        return $name;
    }

    /**
     * @param string $path
     * @throws RuntimeException
     * @return Name
     */
    public static function nameFromPathString(string $path): Name
    {
        if (empty($path)) {
            throw new RuntimeException("Input string is empty");
        }

        $parts = explode('.', $path);
        $name = null;

        foreach ($parts as $part) {
            if (empty($part)) {
                throw new RuntimeException("Empty attribute name found");
            }

            while (strlen($part) > 0) {
                if (str_starts_with($part, '[')) {
                    $endBracketPos = strpos($part, ']');
                    if ($endBracketPos === false) {
                        throw new RuntimeException("Unclosed bracket found");
                    }

                    $number = substr($part, 1, $endBracketPos - 1);
                    if (! ctype_digit($number)) {
                        throw new RuntimeException("Index must be a non-negative integer");
                    }

                    if ($name === null) {
                        throw new RuntimeException("Index used without a preceding attribute name");
                    }

                    $name->index((int) $number);
                    $part = substr($part, $endBracketPos + 1);
                } else {
                    $nextBracketPos = strpos($part, '[');

                    if (str_contains($part, ']') && ($nextBracketPos === false || $nextBracketPos > strpos($part, ']'))) {
                        throw new RuntimeException("Closing bracket ']' found without an opening bracket");
                    }

                    $namePart = $nextBracketPos === false ? $part : substr($part, 0, $nextBracketPos);
                    $part = $nextBracketPos === false ? '' : substr($part, $nextBracketPos);

                    if ($name === null) {
                        $name = new Name($namePart);
                    } else {
                        $name->path($namePart);
                    }
                }
            }
        }

        return $name;
    }

    /**
     * @param string $name
     * @return Key
     */
    public static function key(string $name): Key
    {
        return new Key($name);
    }

    /**
     * @param Condition $condition
     * @return Condition
     */
    public static function not(Condition $condition): Condition
    {
        return new Condition($condition, ConditionTypeEnum::notCond);
    }

    /**
     * @param Condition $condition
     * @return ConditionBuilder
     */
    public static function conditionBuilder(Condition $condition): ConditionBuilder
    {
        return new ConditionBuilder($condition);
    }

    /**
     * @param KeyCondition $partitionKeyCondition
     * @return KeyConditionBuilder
     */
    public static function keyConditionBuilder(KeyCondition $partitionKeyCondition): KeyConditionBuilder
    {
        return new KeyConditionBuilder($partitionKeyCondition);
    }

    /**
     * @return UpdateBuilder
     */
    public static function updateBuilder(): UpdateBuilder
    {
        return new UpdateBuilder();
    }

    /**
     * @param Name ...$names
     * @return ProjectionBuilder
     */
    public static function projectionBuilder(Name ...$names): ProjectionBuilder
    {
        return new ProjectionBuilder(...$names);
    }

    /**
     * @return ExpressionBuilder
     */
    public static function builder(): ExpressionBuilder
    {
        return new ExpressionBuilder();
    }
}

