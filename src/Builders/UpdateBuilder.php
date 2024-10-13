<?php

namespace DynaExp\Builders;

use DynaExp\Builders\Internal\IfNotExists;
use DynaExp\Builders\Internal\NodeInterface;
use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\ActionsSequence;
use DynaExp\Nodes\Update;

final class UpdateBuilder
{
    /**
     * @var array<string, Action[]>
     */
    private array $actions;

    public function __construct()
    {
        $this->actions = [];
    }

    /**
     * @param Path $name
     * @param mixed $value
     * @return UpdateBuilder
     */
    public function add(Path $name, mixed $value) : UpdateBuilder
    {
        $this->actions[ActionTypeEnum::add->name][] =
            new Action($name->getNode(), ActionTypeEnum::add, $value);

        return $this;
    }

    /**
     * @param Path $name
     * @param mixed $value
     * @return UpdateBuilder
     */
    public function delete(Path $name, mixed $value) : UpdateBuilder
    {
        $this->actions[ActionTypeEnum::delete->name][] =
            new Action($name->getNode(), ActionTypeEnum::delete, $value);

        return $this;
    }

    /**
     * @param Path $name
     * @return UpdateBuilder
     */
    public function remove(Path $name) : UpdateBuilder
    {
        $this->actions[ActionTypeEnum::remove->name][] = 
            new Action($name->getNode(), ActionTypeEnum::remove);

        return $this;
    }

    /**
     * @param Path $name
     * @param Operation|Path|IfNotExists|mixed $value
     * @return UpdateBuilder
     */
    public function set(Path $name, mixed $value): UpdateBuilder
    {
        if ($value instanceof NodeInterface) {
            $value = $value->getNode();
        }

        $this->actions[ActionTypeEnum::set->name][] = 
            new Action($name->getNode(), ActionTypeEnum::set, $value);

        return $this;
    }

    /**
     * @return Update
     */
    public function build(): Update
    {
        $updates = [];

        foreach (ActionTypeEnum::cases() as $actionType) {

            if (isset($this->actions[$actionType->name])) {

                $updates[] = new ActionsSequence($actionType, $this->actions[$actionType->name]);
            }
           
        }
     
        return new Update($updates);
    }
}
