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
 * Class base for steps and activity definitions.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class BaseBlockDefinition
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;
    
    /** @var string */
    private $whenExpression = '';

    /** @var string|CollectionInterface */
    private $loopExpression = '';

    /**
     * Constructor.
     *
     * @param string $id The Id of the block.
     */
    protected function __construct(string $id)
    {
        $this->setId($id);
        $this->setName($id);
    }

    /**
     * Returns the Id of the block.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the value of "when" expression.
     *
     * @return string
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
     * @param string|CollectionInterface $expression
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
     * Sets the name of the block.
     *
     * @param string $name
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->assertParameterIsNotEmpty($name, 'name');
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the name of the block.
     */
    public function getName(): string
    {
        return $this->name;
    }

    private function setId(string $id): void
    {
        $this->assertParameterIsNotEmpty($id, 'id');
        $this->id = $id;
    }

    private function assertParameterIsNotEmpty(string $value, string $paramName): void
    {
        if (\strlen(\trim($value)) == 0) {
            throw new InvalidArgumentException("The parameter \"{$paramName}\" cannot be empty.");
        }
    }
}
