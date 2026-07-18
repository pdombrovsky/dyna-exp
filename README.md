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

- **Nodes** – small immutable objects such as `Path`, `Condition`, `Operation`, `Projection`, or `Update`. They hold typed expression data and evaluate themselves through the internal evaluator contract.
- **Factories** – ergonomic wrappers (`Attribute`, `Key`, `AttributeSize`, `DefaultValue`, …) that expose DynamoDB-oriented helpers. One `Attribute` instance can create conditions, updates, projections, search expressions, and aliases without re-parsing strings.
- **Builders** – fluent APIs for assembling nodes (`ConditionBuilder`, `KeyConditionBuilder`, `ProjectionBuilder`, `UpdateBuilder`, `ExpressionBuilder`).
- **Evaluator** – turns nodes into DynamoDB strings, allocates deterministic `ExpressionAttributeNames`/`ExpressionAttributeValues`, keeps alias usage consistent even when nodes are reused, and can optionally normalize nodes through a preprocessor before rendering.
- **ExpressionResult** – a read-only result object with a simple `toArray()` export. You stay in control of marshalling to DynamoDB types, so the library works with any SDK or transport layer.

## Quick Start

```php
use DynaExp\Factories\Attribute;
use DynaExp\Factories\Key;
use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\ExpressionBuilder;

$price = Attribute::create('price');
$stock = Attribute::create('inventory', 'total');

$condition = ConditionBuilder::allOf(
    $price->lessThanEqual(100),
    $stock->greaterThan(0)
)->build();

$expr = (new ExpressionBuilder())
    ->setFilter($condition)
    ->setKeyCondition(
        Key::create('pk')->equal('PRODUCT#123')
    )
    ->build()
    ->toArray();

// [
//   'FilterExpression' => '#0 <= :0 AND #1 > :1',
//   'KeyConditionExpression' => '#2 = :2',
//   'ExpressionAttributeNames' => ['#0' => 'price', '#1' => 'total', '#2' => 'pk'],
//   'ExpressionAttributeValues' => [':0' => 100, ':1' => 0, ':2' => 'PRODUCT#123'],
// ]
```

The builders never mutate state after build time, so the same nodes can be reused in different expressions.

### Evaluation Pipeline

- Each node exposes `evaluate(EvaluatorInterface $evaluator)`.
- `Evaluator` implements that internal evaluator contract, recursively evaluates nested nodes, and centralizes name/value alias allocation.
- `ExpressionBuilder` can receive an optional `ExpressionPreprocessorInterface` and creates a fresh `Evaluator` for every `build()` call.
- If you need domain-specific rewrites before rendering, see `Advanced Customization` below.

### Validation Scope

DynaExp validates local expression-builder invariants: path syntax, path segments, empty builders, and collection node shape. It does not validate your table schema, request shape, key role semantics, or every DynamoDB grammar rule. For example, the package does not know which attributes are partition or sort keys, whether an `ADD` action targets a valid DynamoDB type, or whether a final set of expressions is valid for a specific DynamoDB operation.

# Factories

## Attribute

`Attribute` is the main fluent helper for attribute access in filters, projections, conditions, and updates. Under the hood it wraps an immutable `DynaExp\Nodes\Path`, validates every segment, and keeps DynamoDB-specific metadata such as deterministic string representations, `searchExpression()` output, and stable aliased evaluation output.

A single `Attribute` object can be reused across the whole expression: the helper methods mixed in from `ConditionTrait` and `OperationTrait` let you create equality/range/containment checks, attribute existence/type predicates, arithmetic updates, list append/prepend operations, `if_not_exists`, `size()`, and more – all off the same root path.

Examples:
```php
use DynaExp\Factories\Attribute;
use DynaExp\Evaluation\Evaluator;
use DynaExp\Nodes\Path;

// Programmatic attribute helper
$p = Attribute::create('map', 'nested', 0, 'attr');   // map.nested[0].attr

// From string with quotes to keep dots inside a segment
$p2 = Attribute::fromString('map."a.b"[3].c');        // map.a.b[3].c

// Standalone path node, useful when you need the raw expression operand
$nodePath = Path::fromString('map."a.b"[3].c');

// JMESPath-like search expression (quoted segments). Optional index reset.
$p2->searchExpression();        // "map"."a.b"[3]."c"
$p2->searchExpression(true);    // "map"."a.b"[0]."c"

// JMESPath for marshaled DynamoDB AttributeValue item data.
// The expression is relative to the item object; add Item. or Items[0]. outside if needed.
$p2->marshaledSearchExpression();                          // "map".M."a.b".L[3].M."c"
$p2->marshaledSearchExpression(true);                      // "map".M."a.b".L[0].M."c"

// Evaluation output with aliases
$evaluator = new Evaluator();
$exprPath = $evaluator->evaluate($p2->project());   // "#0.#1[3].#2"
$namesMap = $evaluator->getAttributeNameAliases();  // ['#0' => 'map', '#1' => 'a.b', '#2' => 'c']

// Parent/child helpers
$p3 = Attribute::create('root', 'child');  // root.child
$p3Parent = $p3->parent();             // Attribute for 'root'
$p3Child  = $p3->child('leaf');        // root.child.leaf

// Check ancestry
$nested = $p3->child('leaf', 'branch');
$p3->isParentOf($nested); // true
$p3->project()->relativePathOf($nested->project()); // Path for 'leaf.branch'
count($nested->project()); // 4

$counter = Attribute::create('stats', 'counter');

// Reuse the same path for conditions and update actions
$isNonNegative = $counter->greaterThanEqual(0);                   // Semantics: stats.counter >= :0
$increment = $counter->set($counter->ifNotExists(0)->plus(1));    // Semantics: SET stats.counter = if_not_exists(stats.counter, :1) + :2
```

Notes:
- Capabilities:
  - `project()` exposes the underlying `DynaExp\Nodes\Path` for projection builders or manual evaluation.
  - `searchExpression($resetIndexes = false)` formats a deterministic, JMESPath-compatible string for native/unmarshaled item data. Attribute segments are emitted as quoted identifiers with JSON string escaping.
  - `marshaledSearchExpression($resetIndexes = false)` formats a JMESPath-compatible string for marshaled DynamoDB AttributeValue item data and points to the target AttributeValue wrapper.
  - `parent()`, `child(...)`, `isParentOf(...)`, `lastSegment()`, `Path::relativePathOf(...)`, and `Countable` support safe path-tree navigation.
  - Condition helpers include equality/range checks, `between()`, `in()`, `beginsWith()`, `contains()`, `attributeExists()`, and `attributeType()`.
  - Negative condition helpers include `notEqual()`, `notBetween()`, `notIn()`, `notBeginsWith()`, `notContains()`, `attributeNotExists()`, and `attributeTypeNot()`.
- Parser rules (`Attribute::fromString()` and `DynaExp\Nodes\Path::fromString()`):
  - Dots split attribute segments: `map.nested.attr`
  - Brackets denote list indexes: `list[0][10]`
  - Double quotes wrap a segment to allow dots: `attr1."some.nested.attribute".attr2`
  - Inside quotes, JSON string escapes are supported: use `\"` for a literal quote, `\\` for a literal backslash, and escapes such as `\n`, `\t`, `\/`, or `\uXXXX` when needed
  - Quotes are not allowed inside brackets
- Limitations:
  - Negative indexes, leading-zero indexes except `[0]`, indexes larger than `PHP_INT_MAX`, and empty segments are rejected at construction time (string parser or programmatic API).
  - Invalid JSON escape sequences in quoted segments are rejected.
  - Attribute/path helpers do not marshal attribute values; combine evaluated expressions with your own DynamoDB encoder.
- Values vs expression operands:
  - Plain values are stored in `ExpressionAttributeValues`.
  - Objects passed as values are treated as opaque user payloads. For example, `Attribute::create('a')->equal(Attribute::create('b'))` stores the right-side `Attribute` object as `:0`.
  - To use another path as an expression operand, pass the underlying node explicitly: `Attribute::create('a')->equal(Attribute::create('b')->project())` renders as `#0 = #1`.

## Key

Description:
- Factory to build key condition expression fragments. Use with KeyConditionBuilder to combine.
- DynaExp does not know your table schema and does not validate which attribute is the partition or sort key.

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

## AttributeSize (via Attribute)

Description:
- Wrapper to use the DynamoDB `size()` function on a path, returning a helper with condition methods.

Examples:
```php
use DynaExp\Builders\ConditionBuilder;
use DynaExp\Factories\Attribute;

$sizeCond = Attribute::create('a')->size()->greaterThan(0);  // size(a) > :0

// You can nest size inside other expressions
$existsAndSize = ConditionBuilder::allOf(
    Attribute::create('a')->attributeExists(),
    Attribute::create('a')->size()->lessThanEqual(25)
)->build();
```

Notes:
- Usually created through `Attribute::create(...)->size()`; constructing directly is rarely needed.

## DefaultValue (via Attribute)

Description:
- Wrapper for `if_not_exists(path, value)` to use inside SET operations or nested operations.

Examples:
```php
use DynaExp\Factories\Attribute;

$p = Attribute::create('counter');
$setIfNot = $p->set($p->ifNotExists(0));  // SET counter = if_not_exists(counter, :0)

// Preparing a default payload and storing a backup
$map = Attribute::create('items', 0);
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
use DynaExp\Factories\Attribute;
use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\ExpressionBuilder;

$a = Attribute::create('a');
$b = Attribute::create('b');

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
    ->build()
    ->toArray();

// $ctx === [
//     'FilterExpression' => 'attribute_exists (#0) AND (NOT #1 BETWEEN :0 AND :1 OR #1 IN (:2, :3, :4)) AND contains (#0, :5)',
//     'ExpressionAttributeNames' => ['#0' => 'a', '#1' => 'b'],
//     'ExpressionAttributeValues' => [':0' => 5, ':1' => 10, ':2' => 'x', ':3' => 'y', ':4' => 'z', ':5' => 'x'],
// ];
```

Notes:
- If no initial condition is set, `.and()`/`.or()` require at least two arguments.
- Passing another `ConditionBuilder` into `.and()`/`.or()` wraps that nested builder in parentheses.

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
- An `AND` key condition should not be nested again as `AND` (guarded by the builder).
- `.and()` can be called only once; a second call throws.
- The builder validates only this local shape. It does not validate table schema, partition key role, or sort key role.

## ProjectionBuilder

Description:
- Aggregates projected attributes into a `Projection` node. Inputs must implement `ProjectableInterface`, so both `Attribute` and `Key` can be projected.

Examples:
```php
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Factories\Key;
use DynaExp\Factories\Attribute;

$projection = (new ProjectionBuilder(
    Attribute::create('a'),
    Attribute::create('b'),
    Key::create('pk')
))->build();
```

Notes:
- Projection evaluates to a comma-separated list with aliased names.
- Building an empty projection throws `DynaExp\Exceptions\RuntimeException`.

## UpdateBuilder

Description:
- Collects actions (SET/REMOVE/ADD/DELETE) and groups them by action type before rendering.

Examples:
```php
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Factories\Attribute;
use DynaExp\Builders\ExpressionBuilder;

$counter = Attribute::create('counter');
$deprecatedFlag = Attribute::create('flags', 'deprecated');

$update = (new UpdateBuilder())
    ->add(
        $counter->set(1),            // SET counter = :0
        $deprecatedFlag->remove()    // REMOVE flags.deprecated
    )
    ->build();

$ctx = (new ExpressionBuilder())
    ->setUpdate($update)
    ->build()
    ->toArray();

// Example output:
// $ctx['UpdateExpression'] === 'SET #0 = :0 REMOVE #1.#2'
// $ctx['ExpressionAttributeNames'] === ['#0' => 'counter', '#1' => 'flags', '#2' => 'deprecated']
// $ctx['ExpressionAttributeValues'] === [':0' => 1]
```

### Nested operations for SET

```php
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Factories\Attribute;
use DynaExp\Builders\ExpressionBuilder;

$listAttr = Attribute::create('listAttr');
$counter  = Attribute::create('counter');

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
    ->build()
    ->toArray();

// Example output:
// $ctx['UpdateExpression'] === 'SET #0 = list_append(if_not_exists(#0, :0), :1), #1 = if_not_exists(#1, :2) + :3'
// $ctx['ExpressionAttributeNames'] === ['#0' => 'listAttr', '#1' => 'counter']
// $ctx['ExpressionAttributeValues'] === [':0' => [], ':1' => [1, 2, 3], ':2' => 0, ':3' => 1]
```

Notes:
- DynamoDB evaluates update expression sections internally in the order **REMOVE → SET → ADD → DELETE**. The service accepts any section order in your request payload and normalizes it when processing.
- `listPrepend()` is a convenience helper: DynamoDB supports only `list_append(left, right)`. Implementing a prepend behaviour strictly would require introducing dedicated wrapper objects for values so callers could control argument order, which would make the public API more awkward to use; instead the helper simply swaps the arguments to preserve the mental model (`list_prepend(target, payload)` → `list_append(payload, target)`).
- Building an empty update throws `DynaExp\Exceptions\RuntimeException`.
- Update action sections are grouped by action type and rendered once per type.

### Complex nested update

```php
$itemRoot = Attribute::create('items', 0);
$score = $itemRoot->child('score');
$backup = $itemRoot->child('scoreBackup');
$history = $itemRoot->child('history');
$historyPayload = $itemRoot->child('historyPayload');
$stats = Attribute::create('stats', 'totalScore');
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
    ->build()
    ->toArray();

// SET #0[0].#1 = if_not_exists(#0[0].#1, :0) + if_not_exists(#0[0].#2, :1),
//     #0[0].#3 = list_append(if_not_exists(#0[0].#3, :2), if_not_exists(#0[0].#4, :3))
// ADD #5.#6 :4
// DELETE #0[0].#7 :5
```

## ExpressionBuilder

Description:
- Gathers optional parts (filter/condition/key condition/update/projection), evaluates them through an Evaluator, and returns `ExpressionResult`.

Examples:
```php
use DynaExp\Builders\ConditionBuilder;
use DynaExp\Builders\ExpressionBuilder;
use DynaExp\Builders\ProjectionBuilder;
use DynaExp\Builders\UpdateBuilder;
use DynaExp\Enums\ExpressionTypeEnum;
use DynaExp\Factories\Key;
use DynaExp\Factories\Attribute;

$name = Attribute::create('name');
$price = Attribute::create('price');
$status = Attribute::create('status');

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
    ->build();

$array = $expr->toArray();
// Keys reflect DynamoDB API: ProjectionExpression, FilterExpression, UpdateExpression,
// KeyConditionExpression (if any), plus ExpressionAttributeNames/ExpressionAttributeValues when needed.

$expr->has(ExpressionTypeEnum::filter); // true
```

Notes:
- Empty parts are omitted from the output map.
- `ExpressionResult::has(ExpressionTypeEnum $type)` checks whether a result component exists.
- If you need custom normalization before rendering, pass an `ExpressionPreprocessorInterface` into `ExpressionBuilder`.
- If you need to marshal `ExpressionAttributeValues` for a specific client, do that after `toArray()` on the returned `ExpressionResult`.

# Advanced Customization

## ExpressionPreprocessorInterface

`ExpressionPreprocessorInterface` is an advanced hook for local node rewrites before rendering.

Use it only when the expression tree is valid as-is, but you still need a small amount of application-specific preprocessing before aliases are allocated and strings are rendered.

Typical cases:
- normalize one node shape into another for infrastructure constraints
- inject small compatibility rewrites without changing the public builders

Important rules:
- transform only the current node
- return the original node when no rewrite is needed
- keep the transformation idempotent
- do not traverse child nodes manually; `Evaluator` already does that during recursive rendering

Prefer the regular builders whenever the expression can already be modeled directly. A preprocessor is an edge-case extension point, not the default way to build expressions.

### Example: rewrite one `IN (...)` condition into smaller chunks

DynamoDB allows at most 100 operands in a single `IN` condition. You can technically rewrite a larger input into multiple `IN (...)` groups joined with `OR`, but this does not remove DynamoDB's overall size limits: each individual expression string is limited to 4 KB, and the total size of expression substitution variables is limited to 2 MB. A preprocessor can help with the `IN <= 100` rule, but it is not a general escape hatch for oversized expressions.

```php
use DynaExp\Enums\ConditionTypeEnum;
use DynaExp\Evaluation\ExpressionPreprocessorInterface;
use DynaExp\Nodes\Condition;
use DynaExp\Nodes\EvaluableInterface;

final readonly class SplitLargeInPreprocessor implements ExpressionPreprocessorInterface
{
    public function __construct(private int $chunkSize = 100)
    {
    }

    public function process(EvaluableInterface $node): EvaluableInterface
    {
        if (! $node instanceof Condition || $node->type !== ConditionTypeEnum::inCond) {
            return $node;
        }

        $path = $node->firstOperand();
        $values = $node->tailOperands();

        if (count($values) <= $this->chunkSize) {
            return $node;
        }

        $chunks = array_chunk($values, $this->chunkSize);
        $conditions = array_map(
            fn (array $chunk): Condition => Condition::in($path, ...$chunk),
            $chunks
        );

        return array_reduce(
            array_slice($conditions, 1),
            fn (Condition $carry, Condition $next): Condition => Condition::or($carry, $next),
            $conditions[0]
        );
    }
}
```

This example is intentionally more advanced: it shows that a preprocessor can perform a structural rewrite, not just tweak a scalar value.

Use a preprocessor when:
- the rewrite must happen before alias allocation
- the rule is local to one node at a time
- the existing builders already express the public DSL well enough

Do not use a preprocessor when:
- you can express the logic with the existing builders directly
- you only need to post-process the final `ExpressionResult`
- you want to manually traverse the whole tree

The builder creates a fresh evaluator on every `build()` call, so alias state does not leak between independent compilations.

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

> ⚠️ Recursive references and cyclic structures are not supported. Debug string conversion may fail or produce unusable output for cyclic graphs. Keep arrays and objects finite and acyclic when relying on `__toString()`.

### Attribute value aliases

- Each call to the values aliaser produces a fresh placeholder (`:0`, `:1`, …) even if the same PHP value is passed multiple times. This avoids ambiguity for mutable or complex payloads and keeps the generated expression consistent with the generated value map.

## Errors from Attribute::fromString / Path::fromString

Parser provides specific messages with processed prefix for:
- Empty attribute name (including trailing dot)
- Empty index
- Quoted attribute inside brackets
- Unmatched quote
- Invalid JSON escape sequences inside quoted attributes
- Nested brackets / unmatched bracket
- Invalid index (non-digit)
- Leading-zero indexes except `[0]`
- Indexes larger than `PHP_INT_MAX`
- Negative index via programmatic APIs
- Unsupported segment types via programmatic APIs
- Invalid UTF-8 string segments via programmatic APIs
