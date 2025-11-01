<?php

namespace DynaExp\Nodes\Traits;

use JsonSerializable;
use Stringable;

trait NodesToStringTrait
{
    /**
     * Converts all nodes to string representations.
     *
     * @return string[]
     */
    private function nodesToString(): array
    {
        return array_map([self::class, 'convert'], $this->nodes);
    }

    /**
     * Stable conversion to string for supported types.
     */
    private static function convert(mixed $value): string
    {
        return match (true) {

            is_string($value) => $value,

            is_int($value),

            is_float($value) => (string) $value,

            is_bool($value) => $value ? 'true' : 'false',

            is_array($value) => self::transformArray($value),

            $value === null => 'null',

            is_object($value) => self::transformObject($value),

            is_resource($value) => 'resource(' . get_resource_type($value) . ')',

            default => gettype($value),
        };
    }

    /**
     * @param object $value
     * @return string
     */
    private static function transformObject(object $value): string
    {
        return match (true) {

            $value instanceof Stringable => (string) $value,

            $value instanceof JsonSerializable => self::jsonEncode($value),

            is_callable([$value, 'toArray']) => self::transformArray($value->toArray()),

            default => 'object(' . $value::class . ')',
        };
    }

    /**
     * @param array<int|string, mixed> $value
     * @return string
     */
    private static function transformArray(array $value): string
    {
        $converted = self::convertArray($value);

        return self::jsonEncode($converted);
    }

    /**
     * @param array<int|string, mixed> $data
     * @return array<int|string, mixed>
     */
    private static function convertArray(array $data): array
    {
        $stack = [&$data];

        while ($stack) {
            
            $idx = array_key_last($stack);
            $cur =& $stack[$idx];
            unset($stack[$idx]);

            foreach ($cur as &$value) {

                // --- Nested array ---
                if (is_array($value)) {

                    $stack[] = &$value;

                    continue;
                }

                // --- Object ---
                if (is_object($value)) {

                    $mapped = self::convertObject($value);

                    if (is_array($mapped)) {

                        $value = $mapped;
                        $stack[] = &$value;
                        continue;
                    }

                    $value = $mapped;
                }
            }

            unset($value);
        }

        return $data;
    }

    /**
     * @param object $value
     * @return mixed
     */
    private static function convertObject(object $value): mixed
    {
        return match (true) {

            $value instanceof Stringable => (string) $value,

            $value instanceof JsonSerializable => $value,

            is_callable([$value, 'toArray']) => $value->toArray(),

            default => 'object(' . $value::class . ')',
        };
    }

    /**
     * @param mixed $data
     * @return string
     */
    private static function jsonEncode(mixed $data): string
    {
        $json = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_INVALID_UTF8_SUBSTITUTE
        );

        return $json === false ? var_export($data, true) : $json;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->convertToString($this->nodesToString());
    }

    /**
     * @param string[] $nodes
     * @return string
     */
    abstract protected function convertToString(array $nodes): string;
}
