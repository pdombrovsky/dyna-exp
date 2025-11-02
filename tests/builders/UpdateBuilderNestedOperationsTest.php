<?php

namespace DynaExp\Tests\Builders;

use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Factories\Path;
use PHPUnit\Framework\TestCase;

final class UpdateBuilderNestedOperationsTest extends TestCase
{
    public function testNestedOperationsRenderedCorrectly(): void
    {
        $score = Path::create('items', 0, 'score');
        $backup = Path::create('items', 0, 'scoreBackup');
        $history = Path::create('items', 0, 'history');
        $historyPayload = Path::create('items', 0, 'historyPayload');
        $stats = Path::create('stats', 'totalScore');
        $tags = Path::create('items', 0, 'tags');

        $update = (new UpdateBuilder())
            ->add(
                $score->set(
                    $score
                        ->ifNotExists(0)
                        ->plus(
                            $backup->ifNotExists(1)
                        )
                ),
                $history->set(
                    $history->ifNotExists([])
                        ->listAppend(
                            $historyPayload->ifNotExists([])
                        )
                ),
                $stats->add(10),
                $tags->delete(['legacy'])
            )
            ->build();

        $context = (new ExpressionBuilder())
            ->setUpdate($update)
            ->build()
            ->toArray();

        $this->assertSame('SET #0[0].#1 = if_not_exists(#0[0].#1, :0) + if_not_exists(#0[0].#2, :1), #0[0].#3 = list_append(if_not_exists(#0[0].#3, :2), if_not_exists(#0[0].#4, :3)) ADD #5.#6 :4 DELETE #0[0].#7 :5', $context['UpdateExpression']);

        $this->assertSame(
            [
                "#0" => "items",
                "#1" => "score",
                "#2" => "scoreBackup",
                "#3" => "history",
                "#4" => "historyPayload",
                "#5" => "stats",
                "#6" => "totalScore",
                "#7" => "tags",
            ],

            $context['ExpressionAttributeNames']
        );

        $this->assertSame(
            [
                ":0" => 0,
                ":1" => 1,
                ":2" => [],
                ":3" => [],
                ":4" => 10,
                ":5" => ["legacy"]
            ],
            $context['ExpressionAttributeValues']
        );
    }
}
