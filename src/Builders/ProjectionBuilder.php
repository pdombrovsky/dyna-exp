<?php

namespace DynaExp\Builders;

use DynaExp\Exceptions\RuntimeException;
use DynaExp\Nodes\Path;
use DynaExp\Nodes\Projection;

final class ProjectionBuilder
{
    /**
     * @var Path[]
     */
    private array $nodes;

    /**
     * @param ProjectableInterface ...$paths
     */
    public function __construct(ProjectableInterface ...$paths)
    {
        $this->nodes = [];

        foreach ($paths as $path) {

            $this->nodes[] = $path->project();
        }
    }

    /**
     * @param  ProjectableInterface ...$paths
     * @return ProjectionBuilder
     */
    public function add(ProjectableInterface ...$paths) : ProjectionBuilder
    {
        foreach ($paths as $path) {

            $this->nodes[] = $path->project();
        }

        return $this;
    }

    /**
     * @return Projection
     */
    public function build(): Projection
    {
        if ($this->nodes === []) {
            throw new RuntimeException('Projection requires at least one attribute.');
        }

        return new Projection($this->nodes);
    }
}
