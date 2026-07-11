<?php

namespace DynaExp\Builders;

use DynaExp\Enums\ActionTypeEnum;
use DynaExp\Exceptions\RuntimeException;
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
     * @param Action ...$action
     * @return UpdateBuilder
     */
    public function add(Action ...$action) : UpdateBuilder
    {
        foreach ($action as $current) {

            $this->actions[$current->type->name][] = $current;
        }


        return $this;
    }

    /**
     * @return Update
     */
    public function build(): Update
    {
        if ($this->actions === []) {
            throw new RuntimeException('Update requires at least one actions sequence.');
        }

        $updates = [];

        foreach (ActionTypeEnum::cases() as $actionType) {

            if (isset($this->actions[$actionType->name])) {

                $updates[] = new ActionsSequence($actionType, $this->actions[$actionType->name]);
            }
           
        }
     
        return new Update($updates);
    }
}
