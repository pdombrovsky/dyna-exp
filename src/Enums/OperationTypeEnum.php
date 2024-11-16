<?php

namespace DynaExp\Enums;

enum OperationTypeEnum
{   
    case plusValue;
    case minusValue;
    case listAppend;
    case listPrepend;
    case ifNotExists;
}
