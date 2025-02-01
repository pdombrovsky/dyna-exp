<?php

namespace DynaExp\Enums;

enum ConditionTypeEnum: string
{
	case equalCond = '%s = %s';
	case notEqualCond = '%s <> %s';
	case lessThanCond = '%s < %s';
	case lessThanEqualCond = '%s <= %s';
	case greaterThanCond = '%s > %s';
	case greaterThanEqualCond = '%s >= %s';
	case attrTypeCond = 'attribute_type (%s, %s)';
	case beginsWithCond = 'begins_with (%s, %s)';
	case containsCond = 'contains (%s, %s)';
	case betweenCond = '%s BETWEEN %s AND %s';
	case attrExistsCond = 'attribute_exists (%s)';
	case attrNotExistsCond = 'attribute_not_exists (%s)';
	case inCond = '%s IN (%s)';
	case notCond = 'NOT %s';
	case andCond = '%s AND %s';
	case orCond = '%s OR %s';
	case parenthesesCond = '(%s)';
}
