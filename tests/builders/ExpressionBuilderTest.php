<?php

namespace DynaExp\Tests\Builders;

use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Builders\KeyConditionBuilder;
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Evaluation\EvaluatorFactory;
use DynaExp\Factories\Key;
use DynaExp\Factories\Path;
use PHPUnit\Framework\TestCase;

final class ExpressionBuilderTest extends TestCase
{
    public function testBuildsCompositeExpressionContext(): void
    {
        $customerName = Path::create('customer', 'name');
        $ordersTotal = Path::create('orders', 0, 'total');
        $ordersStatus = Path::create('orders', 0, 'status');
        $lastUpdated = Path::create('meta', 'updatedAt');

        $projection = (new ProjectionBuilder($customerName, $ordersTotal))
            ->add($lastUpdated, Key::create('pk'))
            ->build();

        $keyCondition = KeyConditionBuilder::allOf(
            Key::create('pk')->equal('CUST#123'),
            Key::create('sk')->between('ORDER#0001', 'ORDER#9999')
        )->build();

        $filter = ConditionBuilder::allOf(
            $ordersTotal->greaterThan(100),
            ConditionBuilder::anyOf(
                $ordersStatus->notEqual('CANCELLED'),
                $lastUpdated->attributeExists()
            )
        )->build();

        $update = (new UpdateBuilder())
            ->add(
                $ordersTotal->set($ordersTotal->plus(5)),
                $ordersStatus->set('SHIPPED'),
                $lastUpdated->set($lastUpdated->ifNotExists('1970-01-01T00:00:00Z')),
                Path::create('tags')->add(['priority']),
                Path::create('flags')->delete(['legacy']),
                Path::create('notes', 0)->remove()
            )
            ->build();

        $context = (new ExpressionBuilder())
            ->setProjection($projection)
            ->setKeyCondition($keyCondition)
            ->setFilter($filter)
            ->setUpdate($update)
            ->build(new EvaluatorFactory());

        $array = $context->toArray();

        $this->assertSame(
            [
                'ProjectionExpression' => '#0.#1, #2[0].#3, #4.#5, #6',
                'KeyConditionExpression' => '#6 = :0 AND #7 BETWEEN :1 AND :2',
                'FilterExpression' => '#2[0].#3 > :3 AND (#2[0].#8 <> :4 OR attribute_exists (#4.#5))',
                'UpdateExpression' => 'SET #2[0].#3 = #2[0].#3 + :5, #2[0].#8 = :6, #4.#5 = if_not_exists(#4.#5, :7) ADD #9 :8 DELETE #10 :9 REMOVE #11[0]',
                'ExpressionAttributeNames' => [
                    '#0' => 'customer',
                    '#1' => 'name',
                    '#2' => 'orders',
                    '#3' => 'total',
                    '#4' => 'meta',
                    '#5' => 'updatedAt',
                    '#6' => 'pk',
                    '#7' => 'sk',
                    '#8' => 'status',
                    '#9' => 'tags',
                    '#10' => 'flags',
                    '#11' => 'notes',
                ],
                'ExpressionAttributeValues' => [
                    ':0' => 'CUST#123',
                    ':1' => 'ORDER#0001',
                    ':2' => 'ORDER#9999',
                    ':3' => 100,
                    ':4' => 'CANCELLED',
                    ':5' => 5,
                    ':6' => 'SHIPPED',
                    ':7' => '1970-01-01T00:00:00Z',
                    ':8' => ['priority'],
                    ':9' => ['legacy'],
                ],
            ],
            $array
        );
    }
}
