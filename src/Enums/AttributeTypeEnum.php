<?php

namespace DynaExp\Enums;

enum AttributeTypeEnum: string
{
    case string = 'S';
    case stringSet = 'SS';
    case number = 'N';
    case numberSet = 'NS';
    case binary = 'B';
    case binarySet = 'BS';
    case boolean = 'BOOL';
    case null = 'NULL';
    case list = 'L';
    case map = 'M';
}
