<?php

namespace DynaExp;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Builders\Key;
use DynaExp\Builders\KeyConditionBuilder;
use DynaExp\Builders\Path;
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
     * @return Path
     */
    public static function path(string $attributeName): Path
    {
        return new Path($attributeName);
    }

    /**
     * @param string $attributeName
     * @param string|int ...$segments
     * @return Path
     */
    public static function pathFromSegments(string $attributeName, string|int ...$segments): Path
    {
        $name = new Path($attributeName);

        foreach ($segments as $part) {

            if (is_int($part)) {

                $name->index($part);

                continue;
            }

            $name->segment($part);

        }

        return $name;
    }

    /**
     * @param string $pathString
     * @throws RuntimeException
     * @return Path
     */
    public static function pathFromString(string $pathString): Path
    {
        if (empty($pathString)) {
            throw new RuntimeException("Input string is empty");
        }

        $parts = explode('.', $pathString);
        $path = null;

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

                    if ($path === null) {
                        throw new RuntimeException("Index used without a preceding attribute name");
                    }

                    $path->index((int) $number);
                    $part = substr($part, $endBracketPos + 1);

                } else {

                    $nextBracketPos = strpos($part, '[');

                    if (str_contains($part, ']') && ($nextBracketPos === false || $nextBracketPos > strpos($part, ']'))) {
                        throw new RuntimeException("Closing bracket ']' found without an opening bracket");
                    }

                    $namePart = $nextBracketPos === false ? $part : substr($part, 0, $nextBracketPos);
                    $part = $nextBracketPos === false ? '' : substr($part, $nextBracketPos);

                    if ($path === null) {
                        $path = new Path($namePart);
                    } else {
                        $path->segment($namePart);
                    }

                }
            }
        }

        return $path;
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
     * @param Path ...$names
     * @return ProjectionBuilder
     */
    public static function projectionBuilder(Path ...$names): ProjectionBuilder
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

