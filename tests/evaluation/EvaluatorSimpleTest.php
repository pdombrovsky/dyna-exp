<?php

namespace DynaExp\Tests\Evaluation;

use DynaExp\Enums\AttributeTypeEnum;
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Enums\KeyConditionTypeEnum;
use DynaExp\Enums\OperationTypeEnum;
use DynaExp\Evaluation\Evaluator;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\KeyCondition;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Path;
use DynaExp\Nodes\Size;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EvaluatorSimpleTest extends TestCase
{
    use EvaluatorTestTrait;

    public function testEvaluatePath()
    {
        $path = new Path(['attribute', 'nested1', 2, 'nested2']);

        $evaluator = new Evaluator();

        $this->assertSame(
            '#0.#1[2].#2',
            $path->evaluate($evaluator)
        );

        $this->assertSame(
            [
                '#0' => 'attribute',
                '#1' => 'nested1',
                '#2' => 'nested2'
            ],
            $evaluator->getAttributeNameAliases()
        );

        $this->assertSame(
            [],
            $evaluator->getAttributeValueAliases()
        );
    }

    public function testEvaluateSize()
    {
        $path = new Path(['attribute', 'nested1', 2, 'nested2']);

        $size = new Size([$path]);

        $evaluator = new Evaluator();

        $this->assertSame(
            'size (#0.#1[2].#2)',
            $size->evaluate($evaluator)
        );

        $this->assertSame(
            [
                '#0' => 'attribute',
                '#1' => 'nested1',
                '#2' => 'nested2'
            ],
            $evaluator->getAttributeNameAliases()
        );

        $this->assertSame(
            [],
            $evaluator->getAttributeValueAliases()
        );
    }

    public static function conditionProvider(): array
    {
        $path = new Path(['attribute', 'nested1', 0, 'nested2', 3]);

        return [
            [
                new Condition(ConditionTypeEnum::equalCond, $path, 'value'),
                '#0.#1[0].#2[3] = :0',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 'value'
                ]
            ],
            [
                new Condition(ConditionTypeEnum::notEqualCond, $path, [1, 2, 3]),
                '#0.#1[0].#2[3] <> :0',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => [1, 2, 3]
                ]
            ],
            [
                new Condition(ConditionTypeEnum::lessThanCond, $path, [1, 2, 3]),
                '#0.#1[0].#2[3] < :0',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => [1, 2, 3]
                ]
            ],
            [
                new Condition(ConditionTypeEnum::lessThanEqualCond, $path, [1, 2, 3]),
                '#0.#1[0].#2[3] <= :0',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => [1, 2, 3]
                ]
            ],
            [
                new Condition(ConditionTypeEnum::greaterThanCond, $path, [1, 2, 3]),
                '#0.#1[0].#2[3] > :0',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => [1, 2, 3]
                ]
            ],
            [
                new Condition(ConditionTypeEnum::greaterThanEqualCond, $path, [1, 2, 3]),
                '#0.#1[0].#2[3] >= :0',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => [1, 2, 3]
                ]
            ],
            [
                new Condition(ConditionTypeEnum::attrTypeCond, $path, AttributeTypeEnum::map->value),
                'attribute_type (#0.#1[0].#2[3], :0)',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 'M'
                ]
            ],
            [
                new Condition(ConditionTypeEnum::beginsWithCond, $path, 'abc'),
                'begins_with (#0.#1[0].#2[3], :0)',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 'abc'
                ]
            ],
            [
                new Condition(ConditionTypeEnum::containsCond, $path, 'abc'),
                'contains (#0.#1[0].#2[3], :0)',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 'abc'
                ]
            ],
            [
                new Condition(ConditionTypeEnum::betweenCond, $path, 1, 10),
                '#0.#1[0].#2[3] BETWEEN :0 AND :1',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 1,
                    ':1' => 10
                ]
            ],
            [
                new Condition(ConditionTypeEnum::attrExistsCond, $path),
                'attribute_exists (#0.#1[0].#2[3])',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                []
            ],
            [
                new Condition(ConditionTypeEnum::inCond, $path, 1, 2, 4, 10),
                '#0.#1[0].#2[3] IN (:0, :1, :2, :3)',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 1,
                    ':1' => 2,
                    ':2' => 4,
                    ':3' => 10,
                ]
            ],
            [
                new Condition(ConditionTypeEnum::notCond, new Condition(ConditionTypeEnum::containsCond, $path, 'abc')),
                'NOT contains (#0.#1[0].#2[3], :0)',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 'abc'
                ]
            ],
            [
                new Condition(
                    ConditionTypeEnum::andCond,
                    new Condition(ConditionTypeEnum::greaterThanCond, $path, 5),
                    new Condition(ConditionTypeEnum::lessThanCond, $path, 10)
                ),
                '#0.#1[0].#2[3] > :0 AND #0.#1[0].#2[3] < :1',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 5,
                    ':1' => 10,
                ]
            ],
            [
                new Condition(
                    ConditionTypeEnum::orCond,
                    new Condition(ConditionTypeEnum::greaterThanCond, $path, 25),
                    new Condition(ConditionTypeEnum::lessThanCond, $path, 0)
                ),
                '#0.#1[0].#2[3] > :0 OR #0.#1[0].#2[3] < :1',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 25,
                    ':1' => 0,
                ]
            ],
            [
                new Condition(
                    ConditionTypeEnum::notCond,
                    new Condition(
                        ConditionTypeEnum::parenthesesCond,
                        new Condition(
                            ConditionTypeEnum::orCond,
                            new Condition(ConditionTypeEnum::greaterThanCond, $path, 25),
                            new Condition(ConditionTypeEnum::lessThanCond, $path, 0)
                        ),
                    )
                ),
                'NOT (#0.#1[0].#2[3] > :0 OR #0.#1[0].#2[3] < :1)',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                [
                    ':0' => 25,
                    ':1' => 0,
                ]

            ]
        ];
    }

    #[DataProvider('conditionProvider')]
    public function testEvaluateCondition(Condition $condition, string $evaluatedCondition, array $aliasNames, array $aliasValues)
    {
        $this->testEvaluate($condition, $evaluatedCondition, $aliasNames, $aliasValues);
    }

    public static function keyConditionProvider(): array
    {
        $path = new Path(['keyAttribute']);

        return [
            [
                new KeyCondition(KeyConditionTypeEnum::equalKeyCond, $path, 'value'),
                '#0 = :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 'value'
                ]
            ],
            [
                new KeyCondition(KeyConditionTypeEnum::lessThanKeyCond, $path, 124365),
                '#0 < :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 124365
                ]
            ],
            [
                new KeyCondition(KeyConditionTypeEnum::lessThanEqualKeyCond, $path, 124365),
                '#0 <= :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 124365
                ]
            ],
            [
                new KeyCondition(KeyConditionTypeEnum::greaterThanKeyCond, $path, 124365),
                '#0 > :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 124365
                ]
            ],
            [
                new KeyCondition(KeyConditionTypeEnum::greaterThanEqualKeyCond, $path, 124365),
                '#0 >= :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 124365
                ]
            ],
            [
                new KeyCondition(KeyConditionTypeEnum::beginsWithKeyCond, $path, 'abc'),
                'begins_with (#0, :0)',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 'abc'
                ]
            ],
            [
                new KeyCondition(KeyConditionTypeEnum::betweenKeyCond, $path, 1, 10),
                '#0 BETWEEN :0 AND :1',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 1,
                    ':1' => 10
                ]
            ],
            [
                new KeyCondition(
                    KeyConditionTypeEnum::andKeyCond,
                    new KeyCondition(KeyConditionTypeEnum::equalKeyCond, $path, 'value'),
                    new KeyCondition(KeyConditionTypeEnum::lessThanKeyCond, new Path(['keyAttribute2']), 10)
                ),
                '#0 = :0 AND #1 < :1',
                [
                    '#0' => 'keyAttribute',
                    '#1' => 'keyAttribute2',
                ],
                [
                    ':0' => 'value',
                    ':1' => 10,
                ]
            ],
        ];
    }

    #[DataProvider('keyConditionProvider')]
    public function testEvaluateKeyCondition(KeyCondition $condition, string $evaluatedCondition, array $aliasNames, array $aliasValues)
    {
        $this->testEvaluate($condition, $evaluatedCondition, $aliasNames, $aliasValues);
    }

    public static function operationProvider(): array
    {
        $path = new Path(['someAttribute', 1, 'nestedAttribute']);

        return [
            [
                new Operation(OperationTypeEnum::plusValue, $path, 123),
                '#0[1].#1 + :0',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => 123,
                ]
            ],
            [
                new Operation(OperationTypeEnum::plusValue, $path, new Path(['otherAttibute'])),
                '#0[1].#1 + #2',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'otherAttibute',
                ],
                []
            ],
            [
                new Operation(OperationTypeEnum::minusValue, $path, 123),
                '#0[1].#1 - :0',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => 123,
                ]
            ],
            [
                new Operation(OperationTypeEnum::minusValue, $path, new Path(['otherAttibute'])),
                '#0[1].#1 - #2',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'otherAttibute',
                ],
                []
            ],
            [
                new Operation(OperationTypeEnum::listAppend, $path, [1,2,3]),
                'list_append(#0[1].#1, :0)',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => [1,2,3],
                ]
            ],
            [
                new Operation(OperationTypeEnum::listAppend, $path, new Path(['otherAttibute'])),
                'list_append(#0[1].#1, #2)',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'otherAttibute',
                ],
                []
            ],
            [
                new Operation(OperationTypeEnum::listPrepend, $path, [1,2,3]),
                'list_append(:0, #0[1].#1)',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => [1,2,3],
                ]
            ],
            [
                new Operation(OperationTypeEnum::listPrepend, $path, new Path(['otherAttibute'])),
                'list_append(#2, #0[1].#1)',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'otherAttibute',
                ],
                []
            ]
        ];
    }

    #[DataProvider('operationProvider')]
    public function testEvaluateOperation(Operation $operation, string $evaluatedOperation, array $aliasNames, array $aliasValues)
    {
        $this->testEvaluate($operation, $evaluatedOperation, $aliasNames, $aliasValues);
    }
}
