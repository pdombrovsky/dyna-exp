<?php

namespace DynaExp\Evaluation;

class Names
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

            $alias = '#' . count($this->aliasMap);

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
}
