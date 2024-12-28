<?php

namespace DynaExp\Factories;

use DynaExp\Exceptions\InvalidArgumentException;
use DynaExp\Exceptions\RuntimeException;

final class Create
{   
    /**
     * @param string $attribute
     * @return \DynaExp\Factories\Key
     */
    public function key(string $attribute): Key
    {
        return new Key($attribute);
    }

    /**
     * @param string $attribute
     * @param string|int ...$segments
     * @return \DynaExp\Factories\Path
     */
    public function path(string $attribute, string|int ...$segments): Path
    {
        return new Path($attribute, ...$segments);
    }

    /**
     * Creates path from string.
     * Any dot will be treated as segments splitter.
     * Any square bracket will be treated as index description.
     * 
     * @param string $pathString
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @return \DynaExp\Factories\Path
     */
    public static function pathFromString(string $pathString): Path
    {
        if ($pathString === '') {
            throw new InvalidArgumentException("Input string cannot be empty.");
        }

        $segments = [];
        $buffer = '';
        $previousChar = '';
        $shouldProcessBuffer = false;
        $bracketLevel = 0;

        $length = strlen($pathString);
        $i = 0;

        while ($i < $length) {

            $char = $pathString[$i];

            switch ($char) {
                case '.':
                    if ($bracketLevel > 0) {
                        throw new RuntimeException(sprintf("Invalid character '.' inside brackets. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if ($buffer === '' && ']' !== $previousChar) {
                        throw new RuntimeException(sprintf("Empty attribute name found. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $shouldProcessBuffer = false;
                    break;
                case '[':
                    if ($bracketLevel > 0) {
                        throw new RuntimeException(sprintf("Nested brackets are not allowed. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if (in_array($previousChar, ['', '.'])) {
                        throw new RuntimeException(sprintf("Index used without a preceding attribute name. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $bracketLevel++;
                    $shouldProcessBuffer = false;
                    break;
                case ']':
                    if ($bracketLevel === 0) {
                        throw new RuntimeException(sprintf("Unmatched closing bracket. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if ($buffer === '') {
                        throw new RuntimeException(sprintf("Empty index found. %s", self::processedSymbolsMessage($pathString, $i)));
                    }
                    if (! ctype_digit($buffer)) {
                        throw new RuntimeException(sprintf("Only non-negative integers are allowed in index, '$buffer' given. %s", self::processedSymbolsMessage($pathString, $i)));
                    }

                    $buffer = (int) $buffer;

                    $bracketLevel--;
                    $shouldProcessBuffer = false;
                    break;
                default:
                    $buffer .= $char;
                    $shouldProcessBuffer = true;
            }

            if (! $shouldProcessBuffer && $buffer !== '') {
                $segments[] = $buffer;
                $buffer = '';
            }

            $previousChar = $char;
            $i++;
        }

        if ($bracketLevel !== 0) {
            throw new RuntimeException(sprintf("Unmatched opening bracket. %s", self::processedSymbolsMessage($pathString, $i)));
        }

        if ($buffer) {
            $segments[] = $buffer;
        }

        $attribute = array_shift($segments);

        return new Path($attribute, ...$segments);
    }

    /**
     * @param string $pathString
     * @param int $index
     * @return string
     */
    private static function processedSymbolsMessage(string $pathString, int $index): string
    {
        return sprintf("Processed symbols: '%s'.", substr($pathString, 0, $index));
    }
}
