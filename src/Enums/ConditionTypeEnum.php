<?php

namespace DynaExp\Enums;

enum ConditionTypeEnum
{
	case equalCond;
	case notEqualCond;
	case lessThanCond;
	case lessThanEqualCond;
	case greaterThanCond;
	case greaterThanEqualCond;
	case betweenCond;
	case inCond;
	case attrExistsCond;
	case attrNotExistsCond;
	case attrTypeCond;
	case beginsWithCond;
	case containsCond;
	case andCond;
	case orCond;
	case notCond;
	case parenthesesCond;
}
