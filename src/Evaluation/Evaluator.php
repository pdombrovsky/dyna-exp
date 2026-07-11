<?php

namespace DynaExp\Evaluation;

use DynaExp\Evaluation\Aliases\Names;
use DynaExp\Evaluation\Aliases\Values;
use DynaExp\Nodes\EvaluableInterface;

final class Evaluator implements EvaluatorInterface
{
    /**
     * @var Names
     */
    private Names $aliasNames;

    /**
     * @var Values
     */
    private Values $aliasValues;

    /**
     * @param ?ExpressionPreprocessorInterface $preprocessor Optional pre-evaluation preprocessor.
     */
    public function __construct(private ?ExpressionPreprocessorInterface $preprocessor = null)
    {
        $this->aliasNames = new Names();
        $this->aliasValues = new Values();
    }

    /**
     * Renders expression node to DynamoDB expression string while collecting aliases.
     */
    public function evaluate(EvaluableInterface $node): string
    {
        $node = $this->preprocessor?->process($node) ?? $node;

        return $node->evaluate($this);
    }

    /**
     * @inheritDoc
     */
    public function aliasName(string $name): string
    {
        return $this->aliasNames->alias($name);
    }

    /**
     * @inheritDoc
     */
    public function aliasValue(mixed $value): string
    {
        return $this->aliasValues->alias($value);
    }

    /**
     * @return array<string,string>
     */
    public function getAttributeNameAliases(): array
    {
        return $this->aliasNames->getMap();
    }

    /**
     * @return array<string,mixed>
     */
    public function getAttributeValueAliases(): array
    {
        return $this->aliasValues->getMap();
    }
}
