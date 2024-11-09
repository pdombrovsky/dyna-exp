<?php

use DynaExp\Factories\Path;
use DynaExp\Nodes\Action;
use DynaExp\Nodes\Operation;
use DynaExp\Nodes\Projection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once 'EvaluatorTestTrait.php';

final class EvaluatorComplexTest extends TestCase
{
    use EvaluatorTestTrait;

    /**
     * @return array
     */
    public static function operationProvider(): array
    {
        $somePath = new Path('someAttribute', 1, 'nestedAttribute');
        $anotherPath = new Path('anotherAttribute');
        return [
            [
                $somePath
                    ->ifNotExists(0)
                    ->plus(2),
                'if_not_exists(#0[1].#1, :0) + :1',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => 0,
                    ':1' => 2,
                ]
            ],
            [
                $somePath
                    ->ifNotExists(0)
                    ->plus($anotherPath->ifNotExists(4)),
                'if_not_exists(#0[1].#1, :0) + if_not_exists(#2, :1)',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'anotherAttribute',
                ],
                [
                    ':0' => 0,
                    ':1' => 4,
                ]
            ],
            [
                $somePath
                    ->ifNotExists([1, 2, 3])
                    ->listAppend([4, 5, 6]),
                'list_append(if_not_exists(#0[1].#1, :0), :1)',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => [1, 2, 3],
                    ':1' => [4, 5, 6],
                ]
            ],
            [
                $somePath
                    ->ifNotExists([1, 2, 3])
                    ->listAppend($anotherPath->ifNotExists([4, 5, 6])),
                'list_append(if_not_exists(#0[1].#1, :0), if_not_exists(#2, :1))',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'anotherAttribute',
                ],
                [
                    ':0' => [1, 2, 3],
                    ':1' => [4, 5, 6],
                ]
            ],
        ];
    }

    /**
     * @param DynaExp\Nodes\Operation $operation
     * @param string $evaluatedOperation
     * @param array $aliasNames
     * @param array $aliasValues
     * @return void
     */
    #[DataProvider('operationProvider')]
    public function testEvaluateOperation(Operation $operation, string $evaluatedOperation, array $aliasNames, array $aliasValues)
    {
        $this->testEvaluate($operation, $evaluatedOperation, $aliasNames, $aliasValues);
    }

    /**
     * @return array
     */
    public static function actionProvider(): array
    {
        $somePath = new Path('someAttribute', 1, 'nestedAttribute');
        $anotherPath = new Path('anotherAttribute');
        return [
            [
                $somePath->set($anotherPath->ifNotExists(2)->plus(3)),
                '#0[1].#1 = if_not_exists(#2, :0) + :1',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'anotherAttribute',
                ],
                [
                    ':0' => 2,
                    ':1' => 3,
                ]
            ],
            [
                $somePath->set($somePath->ifNotExists(2)->plus(3)),
                '#0[1].#1 = if_not_exists(#0[1].#1, :0) + :1',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => 2,
                    ':1' => 3,
                ]
            ],
            [
                $somePath->add(2),
                '#0[1].#1 :0',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => 2,
                ]
            ],
            [
                $somePath->delete([1, 2, 3]), //[1,2,3] - represents set
                '#0[1].#1 :0',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                [
                    ':0' => [1, 2, 3],
                ]
            ],
            [
                $somePath->remove(),
                '#0[1].#1',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                []
            ]
        ];
    }

    /**
     * @param DynaExp\Nodes\Action $action
     * @param string $evaluatedAction
     * @param array $aliasNames
     * @param array $aliasValues
     * @return void
     */
    #[DataProvider('actionProvider')]
    public function testEvaluateAction(Action $action, string $evaluatedAction, array $aliasNames, array $aliasValues)
    {
        $this->testEvaluate($action, $evaluatedAction, $aliasNames, $aliasValues);
    }

    /**
     * @return array
     */
    public static function projectionProvider(): array
    {
        $somePath = new Path('someAttribute', 'nestedAttribute');
        $anotherPath = new Path('anotherAttribute');
        $anotherOnePath = new Path('anotherOneAttribute', 'nestedAttribute', 2);
        return [
            [
                new Projection([$somePath->project()]),
                '#0.#1',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                ],
                []
            ],
            [
                new Projection([$somePath->project(), $anotherPath->project()]),
                '#0.#1, #2',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'anotherAttribute',
                ],
                []
            ],
            [
                new Projection([$somePath->project(), $anotherPath->project(), $anotherOnePath->project()]),
                '#0.#1, #2, #3.#1[2]',
                [
                    '#0' => 'someAttribute',
                    '#1' => 'nestedAttribute',
                    '#2' => 'anotherAttribute',
                    '#3' => 'anotherOneAttribute'
                ],
                []
            ]
        ];
    }

    /**
     * @param DynaExp\Nodes\Projection $projection
     * @param string $evaluatedProjection
     * @param array $aliasNames
     * @param array $aliasValues
     * @return void
     */
    #[DataProvider('projectionProvider')]
    public function testEvaluateProjection(Projection $projection, string $evaluatedProjection, array $aliasNames, array $aliasValues)
    {
        $this->testEvaluate($projection, $evaluatedProjection, $aliasNames, $aliasValues);
    }
}
