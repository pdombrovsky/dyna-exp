<?php

namespace DynaExp\Builders;

use Aws\DynamoDb\BinaryValue;
use DynaExp\Builders\IfNotExists;
use DynaExp\Builders\Traits\ConditionTrait;
use DynaExp\Builders\Traits\SetOperationTrait;
use DynaExp\Enums\AttributeTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\Dot;
use DynaExp\Nodes\Index;
use DynaExp\Nodes\Name as NameNode;
use DynaExp\Nodes\Operation;
use RuntimeException;

class Name
{
    use ConditionTrait;
    use SetOperationTrait;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        if (empty($name)) {

            throw new RuntimeException("Empty names are not allowed");
        }

        $this->currentNode = new NameNode($name);
    }

    /**
     * @param string $name
     * @return Name
     */
    public function path(string $name): static
    {
        if (empty($name)) {

            throw new RuntimeException("Empty names are not allowed");
        }

        $this->currentNode = new Dot($this->currentNode, new NameNode($name));

        return $this;
    }

    /**
     * @param int $index
     * @return Name
     */
    public function index(int $index): static
    {
        if ($index < 0) {

            throw new RuntimeException("Index value must be non negative integer");

        }

        $this->currentNode = new Index($this->currentNode, $index);

        return $this;
    }

    /**
     * @param Name $pathBuilder
     * @return Name
     */
    public function add(Name $pathBuilder): static
    {
        $this->currentNode = new Dot($this->currentNode, $pathBuilder->currentNode);

        return $this;
    }

    /**
     * @return Condition
     */
    public function attributeExists(): Condition
    {
        return new Condition($this->currentNode, ConditionTypeEnum::attrExistsCond);
    }

    /**
     * @return Condition
     */
    public function attributeNotExists(): Condition
    {
        return new Condition($this->currentNode, ConditionTypeEnum::attrNotExistsCond);
    }

    /**
     * @param AttributeTypeEnum $type
     * @return Condition
     */
    public function attributeType(AttributeTypeEnum $type): Condition
    {
        return new Condition($this->currentNode, ConditionTypeEnum::attrTypeCond, [$type->value]);
    }

    /**
     * @param int|float|string|BinaryValue $prefix
     * @return Condition
     */
    public function beginsWith(int|float|string|BinaryValue $prefix): Condition
    {
        return new Condition($this->currentNode, ConditionTypeEnum::beginsWithCond, [$prefix]);
    }

    /**
     * @param int|float|string|BinaryValue $contains
     * @return Condition
     */
    public function contains(int|float|string|BinaryValue $contains): Condition
    {
        return new Condition($this->currentNode, ConditionTypeEnum::containsCond, [$contains]);
    }

    /**
     * @return Size
     */
    public function size(): Size
    {
        return new Size($this);
    }

    /**
     * @return Not
     */
    public function not(): Not
    {
        return new Not($this);
    }

    /**
     * @param mixed $value
     * @return Operation
     */
    public function setIfNotExists(mixed $value): Operation
    {
        return new Operation($this->currentNode, OperationTypeEnum::ifNotExists, value: $value);
    }

    /**
     * @param mixed $value
     * @return IfNotExists
     */
    public function ifNotExists(mixed $value): IfNotExists
    {
        return new IfNotExists($this, $value);
    }
}
