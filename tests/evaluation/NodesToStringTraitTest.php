<?php

namespace DynaExp\Tests\Evaluation;

use DynaExp\Nodes\Traits\NodesToStringTrait;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Stringable;

final class NodesToStringTraitTest extends TestCase
{
    private function makeNode(...$nodes): object
    {
        return new class($nodes) {
            use NodesToStringTrait;
            /** @var array<mixed> */
            public array $nodes;
            public function __construct(array $nodes) { $this->nodes = $nodes; }
            public function convertToString(array $convertedNodes): string { return implode('|', $convertedNodes); }
        };
    }

    public function testDeterministicConversion()
    {
        $str = new class implements Stringable {
            public function __toString(): string { return 'S'; }
        };

        $json = new class implements JsonSerializable {
            public function jsonSerialize(): mixed { return ['y' => 2]; }
        };

        $toArray = new class {
            /** @return array<string,int> */
            public function toArray(): array { return ['x' => 1]; }
        };

        $node = $this->makeNode(
            true,
            null,
            123,
            1.5,
            'str',
            ['a' => 1, 'b' => $json],
            $str,
            $toArray
        );

        $actual = (string) $node;
        $this->assertSame('true|null|123|1.5|str|{"a":1,"b":{"y":2}}|S|{"x":1}', $actual);
    }

    public function testFallbackObjectAndNestedArrays()
    {
        $plain = new class() {};
        $named = new class() implements JsonSerializable {
            public function jsonSerialize(): mixed { return ['k' => 'v']; }
        };

        $node = $this->makeNode([
            'nested' => [
                'obj' => $plain,
                'json' => $named,
                'arr' => [1, 2, 3],
            ]
        ]);

        $out = (string) $node;

        $this->assertStringContainsString('"nested":', $out);
        $this->assertStringContainsString('"json":{"k":"v"}', $out);
        $this->assertStringContainsString('"arr":[1,2,3]', $out);
        $this->assertMatchesRegularExpression('/"obj":"object\([^)]+\)"/', $out);
    }

    public function testSameClassObjectsProduceDistinctValues(): void
    {
        $jsonFactory = static function (array $payload): JsonSerializable {
            return new class($payload) implements JsonSerializable {
                public function __construct(private array $payload) {}
                public function jsonSerialize(): mixed { return $this->payload; }
            };
        };

        $node = $this->makeNode(
            $jsonFactory(['a' => 1]),
            $jsonFactory(['a' => 2])
        );

        $parts = explode('|', (string) $node);

        $this->assertSame('{"a":1}', $parts[0]);
        $this->assertSame('{"a":2}', $parts[1]);
    }

    public function testConvertObjectReturnsPayloadForJsonSerializable(): void
    {
        $traitUser = new class {
            use NodesToStringTrait {
                convertObject as public convertObjectProxy;
                jsonEncode as public jsonEncodeProxy;
            }
            public array $nodes = [];
            protected function convertToString(array $nodes): string { return implode('|', $nodes); }
        };

        $json = new class implements JsonSerializable {
            public function jsonSerialize(): mixed { return ['foo' => 'bar']; }
        };

        $result = $traitUser->convertObjectProxy($json);
        $encoded = $traitUser->jsonEncodeProxy($result);

        $this->assertSame('{"foo":"bar"}', $encoded);
    }
}

