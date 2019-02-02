<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Definition;

use InvalidArgumentException;
use Yosymfony\Collection\CollectionInterface;

/**
 * Class base for steps and activities.
 */
class BaseBlockDefinition
{
    /** @var string */
    private $name;
    
    /** @var string */
    private $whenExpression = '';

    /** @var string|CollectionInterface */
    private $loopExpression = '';

    protected function __construct(string $name)
    {
        if (\strlen(\trim($name)) == 0) {
            throw new InvalidArgumentException('The parameter name cannot be empty.');
        }

        $this->name = $name;
    }

    /**
     *  Returns the value of "when" expression.
     */
    public function getWhenExpression(): string
    {
        return $this->whenExpression;
    }

    /**
     * Set the value of "when" expression. Empty string will be evaluated as `true`.
     *
     * @param string $when
     *
     * @return  self
     */
    public function setWhenExpression(string $when)
    {
        $this->whenExpression = $when;

        return $this;
    }

    /**
     * Returns the value of loop expression. It could be a list of elements.
     *
     * @return string|CollectionInterface
     */
    public function getLoopExpression()
    {
        return $this->loopExpression;
    }

    /**
     * Sets the value of the loop expression.
     *
     * @param string|CollectionInterface $loopExpression
     *
     * @return  self
     */
    public function setLoopExpression($expression)
    {
        if ($expression instanceof CollectionInterface === false && \is_string($expression) === false) {
            $message = "A loop should be a string list expression or a collection. Action name: \"{$this->name}\".";
            throw new InvalidArgumentException($message);
        }
        
        $this->loopExpression = $expression;

        return $this;
    }

    /**
     * Returns the value of name.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
