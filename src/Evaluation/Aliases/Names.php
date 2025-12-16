<?php

namespace DynaExp\Evaluation\Aliases;

final class Names
{
    /**
     * @var array<string,string>
     */
    private array $nameAliasMap;

    public function __construct()
    {
        $this->nameAliasMap = [];
    }

    /**
     * @param string $name
     * @return string
     */
    public function alias(string $name): string
    {
        if (isset($this->nameAliasMap[$name])) {

            return $this->nameAliasMap[$name];
        }

        $alias = '#' . $this->count();

        $this->nameAliasMap[$name] = $alias;

        return $alias;
    }

    /**
     * @return array<string,string>
     */
    public function getMap(): array
    {
        $map = [];

        foreach ($this->nameAliasMap as $name => $alias)
        {
          $map[$alias] = (string) $name;
        }

        return $map;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->nameAliasMap);
    }
}
