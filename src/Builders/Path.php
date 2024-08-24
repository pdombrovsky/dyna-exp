<?php

namespace DynaExp\Builders;

use Aws\DynamoDb\BinaryValue;
use DynaExp\Builders\Internal\IfNotExists;
use DynaExp\Builders\Internal\Size;
use DynaExp\Builders\Traits\ConditionTrait;
use DynaExp\Builders\Traits\NodeTrait;
use DynaExp\Builders\Traits\OperationTrait;
use DynaExp\Enums\AttributeTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Interfaces\NodeInterface;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\PathNode;
use RangeException;

final class Path implements NodeInterface
{
    use NodeTrait;
    use ConditionTrait;
    use OperationTrait;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new RangeException("Empty names are not allowed");
        }

        $this->node = new PathNode($name);
    }

    /**
     * @param string $segment
     * @return Path
     */
    public function segment(string $segment): Path
    {
        if (empty($segment)) {
            throw new RangeException("Empty names are not allowed");
        }

        $this->node = new PathNode($this->node, new PathNode($segment));

        return $this;
    }

    /**
     * @param int $index
     * @return Path
     */
    public function index(int $index): Path
    {
        if ($index < 0) {
            throw new RangeException("Index value must be non negative integer");
        }

        $this->node = new PathNode($this->node, $index);

        return $this;
    }

    /**
     * @param Path $path
     * @return Path
     */
    public function add(Path $path): Path
    {
        $this->node = new PathNode($this->node, $path->node);

        return $this;
    }

    /**
     * @return Condition
     */
    public function attributeExists(): Condition
    {
        return new Condition($this->node, ConditionTypeEnum::attrExistsCond);
    }

    /**
     * @return Condition
     */
    public function attributeNotExists(): Condition
    {
        return new Condition($this->node, ConditionTypeEnum::attrNotExistsCond);
    }

    /**
     * @param AttributeTypeEnum $type
     * @return Condition
     */
    public function attributeType(AttributeTypeEnum $type): Condition
    {
        return new Condition($this->node, ConditionTypeEnum::attrTypeCond, [$type->value]);
    }

    /**
     * @param string $prefix
     * @return Condition
     */
    public function beginsWith(string $prefix): Condition
    {
        return new Condition($this->node, ConditionTypeEnum::beginsWithCond, [$prefix]);
    }

    /**
     * @param int|float|string|BinaryValue $contains
     * @return Condition
     */
    public function contains(int|float|string|BinaryValue $contains): Condition
    {
        return new Condition($this->node, ConditionTypeEnum::containsCond, [$contains]);
    }

    /**
     * @return Size
     */
    public function size(): Size
    {
        return new Size($this);
    }

    /**
     * @param mixed $value
     * @return IfNotExists
     */
    public function setIfNotExists(mixed $value): IfNotExists
    {
        return new IfNotExists($this, $value);
    }
}
