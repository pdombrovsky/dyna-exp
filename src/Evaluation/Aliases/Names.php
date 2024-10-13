<?php

namespace DynaExp\Evaluation\Aliases;

final class Names
{
    /**
     * @var string[]
     */
    private array $aliasMap;

    public function __construct()
    {
        $this->aliasMap = [];
    }

    /**
     * @param string $name
     * @return string
     */
    public function alias(string $name): string
    {
        $alias = array_search($name, $this->aliasMap);

        if (false === $alias) {

            $alias = '#' . $this->count();

            $this->aliasMap[$alias] = $name;

        }

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
