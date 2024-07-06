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
        $alias = ':' . count($this->aliasMap);

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
}
