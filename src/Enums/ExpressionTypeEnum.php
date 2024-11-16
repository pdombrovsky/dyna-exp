<?php

namespace DynaExp\Enums;

enum ExpressionTypeEnum: string
{
    case projection = 'ProjectionExpression';
    case keyCondition = 'KeyConditionExpression';
    case filter = 'FilterExpression';
    case condition = 'ConditionExpression';
    case update = 'UpdateExpression';
    case names = 'ExpressionAttributeNames';
    case values = 'ExpressionAttributeValues';
}
