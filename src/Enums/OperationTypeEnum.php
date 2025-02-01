<?php

namespace DynaExp\Enums;

enum OperationTypeEnum: string
{   
    case plusValue = '%s + %s';
    case minusValue = '%s - %s';
    case listAppend = 'list_append(%s, %s)';
    case listPrepend = '';
    case ifNotExists = 'if_not_exists(%s, %s)';
}
