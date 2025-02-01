<?php

namespace DynaExp\Enums;

enum ActionTypeEnum: string
{
    case set = 'SET';
    case add = 'ADD';
    case delete = 'DELETE';
    case remove = 'REMOVE';
}
