<?php

namespace DynaExp\Evaluation\Aliases;

final class Values
{
    /**
     * @var mixed[]
     */
    private array $aliasMap;

    public function __construct()
    {
        $this->aliasMap = [];
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function alias(mixed $value): string
    {
        // Always allocate a fresh alias; duplicate payloads are intentionally not deduplicated
        // to avoid ambiguity with mutable/complex values.
        $alias = ':' . $this->count();

        $this->aliasMap[$alias] = $value;

        return $alias;
    }

    /**
     * @return array<string,mixed>
     */
    public function getMap(): array
    {
        return $this->aliasMap;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->aliasMap);
    }
}
