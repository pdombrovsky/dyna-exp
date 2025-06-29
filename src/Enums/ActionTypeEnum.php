<?php

namespace DynaExp\Enums;

enum ActionTypeEnum: string
{
    case set = 'SET';
    case add = 'ADD';
    case delete = 'DELETE';
    case remove = 'REMOVE';

    /**
     * @return string
     */
    public function fmtString(): string
    {
        return match ($this) {
            self::set => '%s = %s',
            self::add,
            self::delete => '%s %s',
            self::remove => '%s',
        };
    }
}
