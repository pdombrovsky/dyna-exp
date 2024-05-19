<?php

namespace DynaExp\Enums;

enum KeyConditionTypeEnum
{
	case equalKeyCond;
	case lessThanKeyCond;
	case lessThanEqualKeyCond;
	case greaterThanKeyCond;
	case greaterThanEqualKeyCond;
	case betweenKeyCond;
	case beginsWithKeyCond;
	case andKeyCond;
}
