<?php

namespace DynaExp\Builders;

use DynaExp\Builders\Path;
use DynaExp\Interfaces\BuilderInterface;
use DynaExp\Interfaces\EvaluableInterface;
use DynaExp\Nodes\Projection;

final class ProjectionBuilder implements BuilderInterface
{
    /**
     * @var EvaluableInterface[]
     */
    private array $nodes;

    /**
     * @param Path ...$names
     */
    public function __construct(Path ...$names)
    {
        $this->nodes = [];

        foreach ($names as $nameBulder) {

            $this->nodes[] = $nameBulder->getNode();
        }
    }

    /**
     * @param Path ...$names
     * @return ProjectionBuilder
     */
    public function add(Path ...$names) : ProjectionBuilder
    {
        foreach ($names as $nameBuilder) {

            $this->nodes[] = $nameBuilder->getNode();
        }

        return $this;
    }

    /**
     * @return Projection
     */
    public function build(): Projection
    {
        return new Projection($this->nodes);
    }
}
