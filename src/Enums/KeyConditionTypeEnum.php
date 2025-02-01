<?php

namespace DynaExp\Enums;

enum KeyConditionTypeEnum: string
{
	case equalKeyCond = '%s = %s';
	case lessThanKeyCond = '%s < %s';
	case lessThanEqualKeyCond = '%s <= %s';
	case greaterThanKeyCond = '%s > %s';
	case greaterThanEqualKeyCond = '%s >= %s';
	case beginsWithKeyCond = 'begins_with (%s, %s)';
	case betweenKeyCond = '%s BETWEEN %s AND %s';
	case andKeyCond = '%s AND %s';
}
