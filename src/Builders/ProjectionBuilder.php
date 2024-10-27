<?php

namespace DynaExp\Builders;

use DynaExp\Factories\Path;
use DynaExp\Nodes\Projection;

final class ProjectionBuilder
{
    /**
     * @var Path[]
     */
    private array $nodes;

    /**
     * @param Path ...$paths
     */
    public function __construct(Path ...$paths)
    {
        $this->nodes = [];

        foreach ($paths as $path) {

            $this->nodes[] = $path->pathNode;
        }
    }

    /**
     * @param Path ...$paths
     * @return ProjectionBuilder
     */
    public function add(Path ...$paths) : ProjectionBuilder
    {
        foreach ($paths as $path) {

            $this->nodes[] = $path->pathNode;
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
