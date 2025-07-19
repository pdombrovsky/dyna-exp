<?php

namespace DynaExp\Builders;

use DynaExp\Nodes\PathNode;
use DynaExp\Nodes\Projection;

final class ProjectionBuilder
{
    /**
     * @var PathNode[]
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
        return new Projection($this->nodes);
    }
}
