<?php

namespace DynaExp\Factories;

use DynaExp\Factories\Traits\ConditionTrait;
use DynaExp\Nodes\Path;
use DynaExp\Nodes\Size;

final readonly class AttributeSize
{
    use ConditionTrait;

    /**
     * @var Size
     */
    private Size $evaluable;

    /**
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->evaluable = Size::of($path);
    }

    /**
     * @return Size
     */
    protected function evaluable(): Size
    {
        return $this->evaluable;
    }
}
