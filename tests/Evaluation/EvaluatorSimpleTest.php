<?php

namespace DynaExp\Tests\Evaluation;

use DynaExp\Enums\AttributeTypeEnum;
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
        $path = Path::create('attribute', 'nested1', 2, 'nested2');

        $evaluator = new Evaluator();

        $this->assertSame(
            '#0.#1[2].#2',
            $evaluator->evaluate($path)
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
        $path = Path::create('attribute', 'nested1', 2, 'nested2');

        $size = Size::of($path);

        $evaluator = new Evaluator();

        $this->assertSame(
            'size (#0.#1[2].#2)',
            $evaluator->evaluate($size)
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
        $path = Path::create('attribute', 'nested1', 0, 'nested2', 3);

        return [
            [
                Condition::equal($path, 'value'),
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
                Condition::notEqual($path, [1, 2, 3]),
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
                Condition::lessThan($path, [1, 2, 3]),
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
                Condition::lessThanEqual($path, [1, 2, 3]),
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
                Condition::greaterThan($path, [1, 2, 3]),
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
                Condition::greaterThanEqual($path, [1, 2, 3]),
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
                Condition::attributeType($path, AttributeTypeEnum::map->value),
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
                Condition::beginsWith($path, 'abc'),
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
                Condition::contains($path, 'abc'),
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
                Condition::between($path, 1, 10),
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
                Condition::attributeExists($path),
                'attribute_exists (#0.#1[0].#2[3])',
                [
                    '#0' => 'attribute',
                    '#1' => 'nested1',
                    '#2' => 'nested2'
                ],
                []
            ],
            [
                Condition::in($path, 1, 2, 4, 10),
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
                Condition::not(Condition::contains($path, 'abc')),
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
                Condition::and(
                    Condition::greaterThan($path, 5),
                    Condition::lessThan($path, 10)
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
                Condition::or(
                    Condition::greaterThan($path, 25),
                    Condition::lessThan($path, 0)
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
                Condition::not(
                    Condition::parenthesized(
                        Condition::or(
                            Condition::greaterThan($path, 25),
                            Condition::lessThan($path, 0)
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
        $path = Path::create('keyAttribute');

        return [
            [
                KeyCondition::equal($path, 'value'),
                '#0 = :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 'value'
                ]
            ],
            [
                KeyCondition::lessThan($path, 124365),
                '#0 < :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 124365
                ]
            ],
            [
                KeyCondition::lessThanEqual($path, 124365),
                '#0 <= :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 124365
                ]
            ],
            [
                KeyCondition::greaterThan($path, 124365),
                '#0 > :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 124365
                ]
            ],
            [
                KeyCondition::greaterThanEqual($path, 124365),
                '#0 >= :0',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 124365
                ]
            ],
            [
                KeyCondition::beginsWith($path, 'abc'),
                'begins_with (#0, :0)',
                [
                    '#0' => 'keyAttribute',
                ],
                [
                    ':0' => 'abc'
                ]
            ],
            [
                KeyCondition::between($path, 1, 10),
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
                KeyCondition::and(
                    KeyCondition::equal($path, 'value'),
                    KeyCondition::lessThan(Path::create('keyAttribute2'), 10)
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
        $path = Path::create('someAttribute', 1, 'nestedAttribute');

        return [
            [
                Operation::plus($path, 123),
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
                Operation::plus($path, Path::create('otherAttibute')),
                '#0[1].#1 + #2',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'otherAttibute',
                ],
                []
            ],
            [
                Operation::minus($path, 123),
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
                Operation::minus($path, Path::create('otherAttibute')),
                '#0[1].#1 - #2',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'otherAttibute',
                ],
                []
            ],
            [
                Operation::listAppend($path, [1,2,3]),
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
                Operation::listAppend($path, Path::create('otherAttibute')),
                'list_append(#0[1].#1, #2)',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'otherAttibute',
                ],
                []
            ],
            [
                Operation::listPrepend($path, [1,2,3]),
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
                Operation::listPrepend($path, Path::create('otherAttibute')),
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
