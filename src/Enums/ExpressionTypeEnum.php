<?php

namespace DynaExp\Enums;

enum ExpressionTypeEnum
{
    case projection;
    case key;
    case filter;
    case condition;
    case update;
}
