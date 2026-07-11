# Changelog

## [v1.0.0-alpha-15] - 2026-07-11

### Breaking Changes

- `DynaExp\Context\ExpressionContext` was renamed to `DynaExp\Result\ExpressionResult`.
- `ExpressionBuilder::build()` no longer accepts an evaluator factory argument. `ExpressionBuilder` now creates a fresh evaluator for every build.
- `ExpressionResult::toArray()` is now a pure export method and no longer accepts a values-transform callback. Marshal `ExpressionAttributeValues` outside the result object.
- `DynaExp\Evaluation\EvaluatorFactoryInterface` and `DynaExp\Evaluation\EvaluatorFactory` were removed.
- The old public evaluator visitor API was removed. Nodes now render through `EvaluableInterface::evaluate(EvaluatorInterface $evaluator)`, and `EvaluatorInterface` is the internal rendering/aliasing contract.
- `Action`, `Operation`, `KeyCondition`, `Condition`, and `Size` no longer expose public shape-free constructors. Use their named factories or the higher-level `Path`, `Key`, `Size`, and `IfNotExists` helpers.
- `Action`, `Operation`, `KeyCondition`, `Condition`, and `Size` no longer expose public `$nodes` arrays as their shape model. Use semantic accessors such as `firstOperand()`, `tailOperands()`, `secondOperand()`, `target()`, and `argument()`.
- `PathNode` no longer exposes a public raw constructor. Use `PathNode::create(...)` for programmatic paths or `PathNode::fromString(...)` for string paths.
- `Path::fromString(...)` is stricter: quoted segments use JSON string escapes, invalid escape sequences are rejected, leading-zero indexes are rejected except `[0]`, and indexes larger than `PHP_INT_MAX` are rejected.
- `Projection`, `ActionsSequence`, and `Update` now reject invalid collection shapes at construction time. Empty direct constructor payloads throw `InvalidArgumentException`; empty `ProjectionBuilder` and `UpdateBuilder` builds throw `RuntimeException`.
- `Projection` now models projection paths only and requires `PathNode` entries.
- `ActionsSequence` now requires `Action` entries whose action type matches the sequence type.
- `Update` now rejects duplicate action sequences of the same type.
- `KeyConditionBuilder::and()` now fails fast when called more than once instead of overwriting the previous right-hand key condition.

### Added

- `ExpressionPreprocessorInterface` for advanced local node rewrites before rendering.
- `ExpressionOperandInterface` for helper objects that can explicitly unwrap to expression nodes in update operations.
- `PathNode::fromString(...)` and an internal `PathStringParser` for centralized path parsing.
- `PathNode::marshaledSearchExpression(...)` and `Path::marshaledSearchExpression(...)` for JMESPath-compatible lookup paths inside marshaled DynamoDB AttributeValue data.
- `PathNode` now implements `Countable`.
- `PathNode::relativePathOf(...)` for deriving a child path relative to a base path.
- Focused tests for key-condition builder guards, empty builders, collection node shape invariants, evaluator preprocessors, path validation, projected path operands in conditions, and `ExpressionResult`.

### Changed

- `Evaluator` is now the single rendering entrypoint. It recursively evaluates nodes, owns name/value alias allocation, and applies an optional preprocessor before rendering each current node.
- `NodesToStringTrait` now provides deterministic debug/test string conversion for scalars, arrays, `Stringable`, `JsonSerializable`, `toArray()` objects, resources, and fallback objects.
- `ConditionBuilder` and `KeyConditionBuilder` now build through node named factories instead of raw constructors.
- `Path`, `Key`, `Size`, and `IfNotExists` now build nodes through named factories and explicit operand interfaces instead of the removed abstract node wrapper.
- `Path::create(...)`, `Path::fromString(...)`, and `Key::create(...)` remain untrusted AST entrypoints and validate local path shape only.
- `KeyConditionBuilder` remains schema-agnostic. It guards builder shape but does not validate table schema, partition key role, or sort key role.
- `Path` objects passed as values remain opaque user payloads. Use `Path::create(...)->project()` explicitly when a path should be rendered as an expression operand.
- Composer metadata now uses the `condition-expression` keyword instead of `query`.
- Composer requirements now explicitly list `ext-ctype` and `ext-json`.

### Documentation

- README now documents the evaluation pipeline, validation scope, expression preprocessor contract, marshaled search expressions, stricter path parser behavior, and the difference between opaque values and projected expression operands.
- README now positions DynaExp as an expression builder rather than a full DynamoDB query/request builder.
- README now describes recursive/cyclic debug string conversion as unsupported and potentially failing, rather than promising a controlled exception.

### Migration Notes

- Replace `DynaExp\Context\ExpressionContext` imports/usages with `DynaExp\Result\ExpressionResult`.
- Replace `ExpressionBuilder::build($factory)` with `ExpressionBuilder::__construct(?ExpressionPreprocessorInterface $preprocessor = null)` plus `build()`.
- Move any `ExpressionAttributeValues` marshalling/transformation outside `ExpressionResult::toArray()`.
- Remove `EvaluatorFactoryInterface` / `EvaluatorFactory` usages.
- Update custom node/preprocessor code to use semantic accessors instead of public `$nodes` arrays.
- Replace direct `new PathNode(...)` calls with `PathNode::create(...)` or `PathNode::fromString(...)`.
- Replace direct `new Action(...)`, `new Operation(...)`, `new KeyCondition(...)`, `new Condition(...)`, and `new Size(...)` calls with named factories or public higher-level factory helpers.
- If a path should be used as an expression operand in a condition, pass `Path::create(...)->project()`. Passing the `Path` object itself keeps it as an opaque value alias.
