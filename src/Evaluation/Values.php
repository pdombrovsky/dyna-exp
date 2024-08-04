<?php

namespace DynaExp\Evaluation;

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
        $alias = ':' . $this->count();

        $this->aliasMap[$alias] = $value;

        return $alias;
    }

    /**
     * @return array
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
