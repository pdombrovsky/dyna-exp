<?php

namespace DynaExp\Nodes;

use DynaExp\Evaluation\EvaluatorInterface;
use Stringable;

final readonly class PathNode implements EvaluableInterface, Stringable
{
    /**
     * @param string|PathNode $left
     * @param null|int|PathNode $right
     */
    public function __construct(public string|PathNode $left, public null|int|PathNode $right = null)
    {  
    }

    /**
     * @param EvaluatorInterface $evaluator
     * @return string
     */
    public function evaluate(EvaluatorInterface $evaluator): string
    {
        return $evaluator->evaluatePathNode($this);
    }

    /**
     * @inheritDoc
     */
    public function __tostring(): string
    {
        $rightString = match (true) {
            is_int($this->right)  => "[$this->right]",
            null !== $this->right => ".$this->right",

            default => ''
        };

        return (string) $this->left . $rightString;
    }
}
