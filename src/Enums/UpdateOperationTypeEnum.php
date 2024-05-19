<?php

namespace DynaExp\Enums;

enum UpdateOperationTypeEnum: string
{
    case add = 'ADD';
    case delete = 'DELETE';
    case remove = 'REMOVE';
    case set = 'SET';
}
