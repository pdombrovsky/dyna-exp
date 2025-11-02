<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;
use DynaExp\Nodes\EvaluableInterface;
use DynaExp\Exceptions\InvalidArgumentException;
use Stringable;

final readonly class PathNode implements Stringable, EvaluableInterface
{
    /**
     * Creates PathNode from segments.
     * 
     * @param array<string|int> $segments
     */
    public function __construct(public array $segments)
    {
    }

    /**
     * Creates validated PathNode from segments.
     *
     * @param string|int ...$segments
     * @throws InvalidArgumentException
     */
    public static function create(string $attribute, string|int ...$segments): self
    {
        if ($attribute === '') {

            return throw new InvalidArgumentException("Attribute can not be empty string.");
        }

        self::validateSegments($segments);

        return new self([$attribute, ...$segments]);
    }

    /**
     * @param array<string|int> $segments
     * @throws InvalidArgumentException
     * @return void
     */
    private static function validateSegments(array $segments): void
    {
        $validationMessage = self::getSegmentsValidationMessage($segments);

        if ($validationMessage) {

            throw new InvalidArgumentException($validationMessage);
        }
    }

    /**
     * @param array<string|int> $segments
     * @return string
     */
    private static function getSegmentsValidationMessage(array $segments): string
    {
        $checkedSegments = [];
        $errorMessage = '';
        foreach ($segments as $segment) {

            if (is_int($segment) && $segment < 0) {

                $errorMessage = "Index can not be negative, '$segment' given.";
                break;
            }

            if ($segment === '') {

                $errorMessage = 'Path segment can not be empty string.';
                break;
            }

            $checkedSegments[] = $segment;
        }

        if ($errorMessage) {

            $checked = (new PathNode($checkedSegments))->__tostring();

            return "Wrong path segment found after: '$checked'. $errorMessage";
        }

        return '';
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluatePath($this);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->convertToString($this->segments);
    }

    /**
     * Returns JMESPath search expression
     * 
     * @param bool $resetIndexes
     * @return string
     */
    public function searchExpression(bool $resetIndexes = false): string
    {
        $segments = [];

        foreach ($this->segments as $segment) {

            if (is_int($segment)) {

                $segments[] = $resetIndexes ? 0 : $segment;

            } else {

                $segments[] = self::wrapSegment($segment);

            }
        }

        return $this->convertToString($segments);
    }

    /**
     * Escapes backslashes and double quotes for search expression quoting.
     */
    private static function wrapSegment(string $segment): string
    {
        $escaped = strtr($segment, ["\\" => "\\\\", '"' => '\\"']);

        return "\"$escaped\"";
    }

    /**
     * @inheritDoc
     */
    public function convertToString(array $convertedNodes): string
    {
        $parts = [];
        $lastIndex = -1;
        foreach ($convertedNodes as $segment) {

            if (is_int($segment)) {

                $parts[$lastIndex] .= "[$segment]";

            } else {

                $parts[++$lastIndex] = $segment;
            }
        }
        
        return implode('.', $parts);
    }

    /**
     * Returns parent path node if exists
     * 
     * @return PathNode
     */
    public function parent(): ?self
    {
        $parentSegments = array_slice($this->segments, 0, -1);

        return $parentSegments ? new self($parentSegments) : null;
    }

    /**
     * Returns child path node for given segments
     * 
     * @param array<string|int> $segments
     * @return PathNode
     */
    public function child(array $segments): self
    {
        self::validateSegments($segments);

        return new self([...$this->segments, ...$segments]);
    }

    /**
     * Check if the current node is parent of other
     * 
     * @param PathNode $other
     * @return bool
     */
    public function isParentOf(PathNode $other): bool
    {
        $p = $this->segments;
        $c = $other->segments;

        if (count($p) >= count($c)) {

            return false;
        }

        return array_slice($c, 0, count($p)) === $p;
    }

    /**
     * Returns last segment for given path
     * 
     * @return int|string
     */
    public function lastSegment(): int|string
    {
        return $this->segments[array_key_last($this->segments)];
    }
}
