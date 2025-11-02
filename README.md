# DynaExp

Build DynamoDB expressions (ConditionExpression, FilterExpression, KeyConditionExpression, UpdateExpression, ProjectionExpression) with a small set of typed helpers. DynaExp keeps the verbose string juggling out of your application code while staying close to native DynamoDB semantics.

## Requirements

- PHP >= 8.2

## Installation

> ⚠️ The library is currently in **alpha**. Interfaces may change between releases until we hit the stable 1.x line.

```bash
composer require pdombrovsky/dyna-exp:^1.0@alpha
```

## Overview

- **Nodes** – small immutable objects such as `Condition`, `Operation`, `Projection`, `Update`, or `PathNode`. They hold the typed data that eventually becomes part of an expression string and remember everything they need (segments, traversal helpers, aliases, etc.).
- **Factories** – ergonomic wrappers (`Path`, `Key`, `Size`, `IfNotExists`, …) that expose DynamoDB-oriented helpers. One `Path` instance can create conditions, updates, projections, search expressions, and aliases without re-parsing strings.
- **Builders** – fluent APIs for assembling nodes (`ConditionBuilder`, `KeyConditionBuilder`, `ProjectionBuilder`, `UpdateBuilder`, `ExpressionBuilder`).
- **Evaluator** – turns nodes into DynamoDB strings, allocates deterministic `ExpressionAttributeNames`/`ExpressionAttributeValues`, and keeps alias usage consistent even when nodes are reused.
- **ExpressionContext** – a read-only result object with a convenient `toArray()` method (plus optional value transforms). You stay in control of marshalling to DynamoDB types, so the library works with any SDK or transport layer.

## Quick Start

```php
use DynaExp\Factories\Path;
use DynaExp\Factories\Key;
use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Evaluation\EvaluatorFactory;

$price = Path::create('price');
$stock = Path::create('inventory', 'total');

$condition = ConditionBuilder::allOf(
    $price->lessThanEqual(100),
    $stock->greaterThan(0)
)->build();

$expr = (new ExpressionBuilder())
    ->setFilter($condition)
    ->setKeyCondition(
        Key::create('pk')->equal('PRODUCT#123')
    )
    ->build(new EvaluatorFactory())
    ->toArray();

// [
//   'FilterExpression' => '#0 <= :0 AND #1 > :1',
//   'KeyConditionExpression' => '#2 = :2',
//   'ExpressionAttributeNames' => ['#0' => 'price', '#1' => 'total', '#2' => 'pk'],
//   'ExpressionAttributeValues' => [':0' => 100, ':1' => 0, ':2' => 'PRODUCT#123'],
// ]
```

The builders never mutate state after build time, so the same nodes can be reused in different expressions.

# Factories

## Path

`Path` is the main building block for attribute access in filters, projections, key conditions, and updates. Under the hood it wraps an immutable `PathNode`, validates every segment, and keeps DynamoDB-specific metadata such as deterministic string representations, `searchExpression()` output, and alias-friendly evaluation.  

A single `Path` object can be reused across the whole expression: the helper methods mixed in from `ConditionTrait` and `OperationTrait` let you create equality/range/containment checks, attribute existence/type predicates, arithmetic updates, list append/prepend operations, `if_not_exists`, `size()`, and more – all off the same root path.

Examples:
```php
use DynaExp\Factories\Path;
use DynaExp\Evaluation\Evaluator;

// Programmatic path
$p = Path::create('map', 'nested', 0, 'attr');   // map.nested[0].attr

// From string with quotes to keep dots inside a segment
$p2 = Path::fromString('map."a.b"[3].c');       // map.a.b[3].c

// JMESPath-like search expression (quoted segments). Optional index reset.
$p2->searchExpression();        // "map"."a.b"[3]."c"
$p2->searchExpression(true);    // "map"."a.b"[0]."c"

// Evaluation output with aliases
$evaluator = new Evaluator();
$exprPath = $p2->project()->evaluate($evaluator);   // "#0.#1[3].#2"
$namesMap = $evaluator->getAttributeNameAliases();  // ['#0' => 'map', '#1' => 'a.b', '#2' => 'c']

// Parent/child helpers
$p3 = Path::create('root', 'child');  // root.child
$p3Parent = $p3->parent();             // Path for 'root'
$p3Child  = $p3->child('leaf');        // root.child.leaf

// Check ancestry
$nested = $p3->child('leaf', 'branch');
$p3->isParentOf($nested); // true

$counter = Path::create('stats', 'counter');

// Reuse the same path for conditions and update actions
$isNonNegative = $counter->greaterThanEqual(0);                   // Semantics: stats.counter >= :0
$increment = $counter->set($counter->ifNotExists(0)->plus(1));    // Semantics: SET stats.counter = if_not_exists(stats.counter, :1) + :2
```

Notes:
- Capabilities:
  - `project()` exposes the underlying PathNode for projection builders or manual evaluation.
  - `searchExpression($resetIndexes = false)` formats a deterministic, JMESPath-compatible string.
  - `parent()`, `child(...)`, `isParentOf(...)`, and `lastSegment()` let you navigate the path tree safely.
- Parser rules (Path::fromString):
  - Dots split attribute segments: `map.nested.attr`
  - Brackets denote list indexes: `list[0][10]`
  - Double quotes wrap a segment to allow dots: `attr1."some.nested.attribute".attr2`
  - Inside quotes only `\"` escapes a quote; backslash is otherwise literal
  - Quotes are not allowed inside brackets
- Limitations:
  - Negative indexes and empty segments are rejected at construction time (string parser or programmatic API).
  - Quoted segments support only `\"` escaping; other escape sequences are treated literally.
  - Path does not marshal attribute values; combine evaluated expressions with your own DynamoDB encoder.

## Key

Description:
- Factory to build key conditions (hash/range) for queries. Use with KeyConditionBuilder to combine.

Examples:
```php
use DynaExp\Factories\Key;
use DynaExp\Builders\KeyConditionBuilder;

$hash  = Key::create('pk')->equal('H');
$range = Key::create('sk')->between(100, 200);

$kc = (new KeyConditionBuilder($hash))
    ->and($range)
    ->build();
```

Notes:
- Other helpers on Key: `beginsWith()`, `greaterThan()`, `lessThanEqual()`, etc.

## Size (via Path)

Description:
- Wrapper to use the DynamoDB `size()` function on a path, returning a factory with condition helpers.

Examples:
```php
use DynaExp\Factories\Path;

$sizeCond = Path::create('a')->size()->greaterThan(0);  // size(a) > :0

// You can nest size inside other expressions
$existsAndSize = ConditionBuilder::allOf(
    Path::create('a')->attributeExists(),
    Path::create('a')->size()->lessThanEqual(25)
)->build();
```

Notes:
- Usually created through `Path::create(...)->size()`; constructing directly is rarely needed.

## IfNotExists (via Path)

Description:
- Wrapper for `if_not_exists(path, value)` to use inside SET operations or nested operations.

Examples:
```php
use DynaExp\Factories\Path;

$p = Path::create('counter');
$setIfNot = $p->set($p->ifNotExists(0));  // SET counter = if_not_exists(counter, :0)

// Preparing a default payload and storing a backup
$map = Path::create('items', 0);
$score = $map->child('score');
$backup = $map->child('backup');
$update = (new DynaExp\Builders\UpdateBuilder())
    ->add(
        $score->set(
            $score->ifNotExists(0)->plus($backup->ifNotExists(1))
        ),
        $backup->set($score->ifNotExists(0)),
    )
    ->build();
```

Notes:
- Typically used as a nested value in `set()` or arithmetic operations.

# Builders

## ConditionBuilder

Description:
- Fluent AND/OR composition of conditions. Supports passing other builders (auto-parenthesized evaluation inside) and static constructors.

Examples:
```php
use DynaExp\Factories\Path;
use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Evaluation\EvaluatorFactory;

$a = Path::create('a');
$b = Path::create('b');

$nested = (new ConditionBuilder($a->attributeExists()))
    ->and(
        ConditionBuilder::anyOf(
            $b->notBetween(5, 10), // renders as: NOT b BETWEEN ...
            $b->in('x', 'y', 'z')  // renders as: b IN (...)
        ),
    )
    ->and($a->contains('x'))
    ->build();

// Evaluate to see final strings/aliases
$ctx = (new ExpressionBuilder())
    ->setFilter($nested)
    ->build(new EvaluatorFactory())
    ->toArray();

// $ctx === [
//     'FilterExpression' => 'attribute_exists (#0) AND (NOT #1 BETWEEN :0 AND :1 OR #1 IN (:2, :3, :4)) AND contains (#0, :5)',
//     'ExpressionAttributeNames' => ['#0' => 'a', '#1' => 'b'],
//     'ExpressionAttributeValues' => [':0' => 5, ':1' => 10, ':2' => 'x', ':3' => 'y', ':4' => 'z', ':5' => 'x'],
// ];
```

Notes:
- If no initial condition is set, `.and()`/`.or()` require at least two arguments.

## KeyConditionBuilder

Description:
- Combines left and right key conditions with AND. Left condition is required; right is optional.

Examples:
```php
use DynaExp\Factories\Key;
use DynaExp\Builders\KeyConditionBuilder;

$left  = Key::create('pk')->equal('H');
$right = Key::create('sk')->beginsWith('ORD#');

$keyCond = (new KeyConditionBuilder($left))
    ->and($right)
    ->build();
```

Notes:
- A `AND` key condition should not be nested again as `AND` (guarded by the builder).

## ProjectionBuilder

Description:
- Aggregates projected attributes (paths) into a `Projection` node.

Examples:
```php
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Factories\Path;

$projection = (new ProjectionBuilder(
    Path::create('a'),
    Path::create('b')
))->build();
```

Notes:
- Projection evaluates to a comma-separated list with aliased names.

## UpdateBuilder

Description:
- Collects actions (SET/REMOVE/ADD/DELETE) and groups them by action type before rendering.

Examples:
```php
use DynaExp\Builders\UpdateBuilder;
  use DynaExp\Factories\Path;
  use DynaExp\Builders\ExpressionBuilder;
  use DynaExp\Evaluation\EvaluatorFactory;
  
  $counter = Path::create('counter');
  $deprecatedFlag = Path::create('flags', 'deprecated');
  
  $update = (new UpdateBuilder())
      ->add(
          $counter->set(1),            // SET counter = :0
          $deprecatedFlag->remove()    // REMOVE flags.deprecated
      )
      ->build();
  
  $ctx = (new ExpressionBuilder())
      ->setUpdate($update)
      ->build(new EvaluatorFactory())
      ->toArray();
  
  // Example output:
  // $ctx['UpdateExpression'] === 'SET #0 = :0 REMOVE #1.#2'
  // $ctx['ExpressionAttributeNames'] === ['#0' => 'counter', '#1' => 'flags', '#2' => 'deprecated']
  // $ctx['ExpressionAttributeValues'] === [':0' => 1]
  ```

### Nested operations for SET

```php
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Factories\Path;
use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Evaluation\EvaluatorFactory;

$listAttr = Path::create('listAttr');
$counter  = Path::create('counter');

$appendItems = $listAttr->set(
    $listAttr->ifNotExists([])->listAppend([1, 2, 3])
);

$incrementCounter = $counter->set(
    $counter->ifNotExists(0)->plus(1)
);

$update = (new UpdateBuilder())
    ->add($appendItems, $incrementCounter)
    ->build();

$ctx = (new ExpressionBuilder())
    ->setUpdate($update)
    ->build(new EvaluatorFactory())
    ->toArray();

// Example output:
// $ctx['UpdateExpression'] === 'SET #0 = list_append(if_not_exists(#0, :0), :1), #1 = if_not_exists(#1, :2) + :3'
// $ctx['ExpressionAttributeNames'] === ['#0' => 'listAttr', '#1' => 'counter']
// $ctx['ExpressionAttributeValues'] === [':0' => [], ':1' => [1, 2, 3], ':2' => 0, ':3' => 1]
```

Notes:
- DynamoDB evaluates update expression sections internally in the order **REMOVE → SET → ADD → DELETE**. The service accepts any section order in your request payload and normalizes it when processing.
- `listPrepend()` is a convenience helper: DynamoDB supports only `list_append(left, right)`. Implementing a prepend behaviour strictly would require introducing dedicated wrapper objects for values so callers could control argument order, which would make the public API more awkward to use; instead the helper simply swaps the arguments to preserve the mental model (`list_prepend(target, payload)` → `list_append(payload, target)`).

### Complex nested update

```php
$itemRoot = Path::create('items', 0);
$score = $itemRoot->child('score');
$backup = $itemRoot->child('scoreBackup');
$history = $itemRoot->child('history');
$historyPayload = $itemRoot->child('historyPayload');
$stats = Path::create('stats', 'totalScore');
$tags = $itemRoot->child('tags');

$update = (new UpdateBuilder())
    ->add(
        $score->set(
            $score->ifNotExists(0)->plus($backup->ifNotExists(1))
        ),
        $history->set(
            $history->ifNotExists([])->listAppend(
                $historyPayload->ifNotExists([])
            )
        ),
        $stats->add(10),
        $tags->delete(['legacy'])
    )
    ->build();

$ctx = (new ExpressionBuilder())
    ->setUpdate($update)
    ->build(new EvaluatorFactory())
    ->toArray();

// SET #0[0].#1 = if_not_exists(#0[0].#1, :0) + if_not_exists(#0[0].#2, :1),
//     #0[0].#3 = list_append(if_not_exists(#0[0].#3, :2), if_not_exists(#0[0].#4, :3))
// ADD #5.#6 :4
// DELETE #0[0].#7 :5
```

## ExpressionBuilder

Description:
- Gathers optional parts (filter/condition/key condition/update/projection), evaluates through an Evaluator, and returns ExpressionContext.

Examples:
```php
use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Evaluation\EvaluatorFactory;
use DynaExp\Factories\Key;
use DynaExp\Factories\Path;

$name = Path::create('name');
$price = Path::create('price');
$status = Path::create('status');

$filter = ConditionBuilder::allOf(
    $price->lessThan(100),
    $status->equal('ACTIVE')
)->build();

$projection = (new ProjectionBuilder($name, $price))->build();

$keyCondition = Key::create('pk')->equal('PRODUCT#123');

$update = (new UpdateBuilder())
    ->add($status->set('ACTIVE'))
    ->build();

$expr = (new ExpressionBuilder())
    ->setFilter($filter)
    ->setProjection($projection)
    ->setKeyCondition($keyCondition)
    ->setUpdate($update)
    ->build(new EvaluatorFactory());

$array = $expr->toArray();
// Keys reflect DynamoDB API: ProjectionExpression, FilterExpression, UpdateExpression,
// KeyConditionExpression (if any), plus ExpressionAttributeNames/ExpressionAttributeValues when needed.

// Optional value transformation
$array = $expr->toArray(function (array $values) {
    // Convert your domain/custom types to wire format here
    return $values;
});
```

Notes:
- Empty parts are omitted from the output map.

# Supporting Types

## Deterministic String Conversion (debug/tests)

Nodes that implement `Stringable` or contain arrays/objects use a deterministic conversion in `NodesToStringTrait`:
- Stringable -> `__toString()`
- JsonSerializable -> json-encode `jsonSerialize()`
- Objects with `toArray()` -> json-encode that array
- Arrays -> json-encode recursively (stable output)
- Scalars -> `true|false|null`, numbers, strings
- Other objects -> `object(FQCN)` marker

Note: these conversions are intended for debugging, tests and logs only. Do not use them to build wire payloads for DynamoDB; use the evaluator output and, if needed, marshal values with your SDK/adapter.

> ⚠️ Recursive references and cyclic structures are not supported. Attempting to serialize such data will trigger an exception. Ensure arrays and objects form a finite, acyclic graph.

### Attribute value aliases

- Each call to the values aliaser produces a fresh placeholder (`:0`, `:1`, …) even if the same PHP value is passed multiple times. This avoids ambiguity for mutable or complex payloads and keeps the generated expression consistent with the generated value map.

## Errors from Path::fromString

Parser provides specific messages with processed prefix for:
- Empty attribute name (including trailing dot)
- Quoted attribute inside brackets
- Unmatched quote
- Nested brackets / unmatched bracket
- Invalid index (non-digit), negative index via programmatic APIs
