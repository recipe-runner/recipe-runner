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

use RecipeRunner\RecipeRunner\Module\Invocation\Method;

class ActionDefinition extends BaseBlockDefinition
{
    /** @var string */
    private $variableName = '';

    /** @var Method */
    private $method;

    /**
     * Constructor.
     *
     * @param string $name The activity's name.
     */
    public function __construct(string $id, Method $method)
    {
        parent::__construct($id);
        $this->method = $method;
    }

    /**
     * Returns the method.
     *
     * @return Method
     */
    public function getMethod(): Method
    {
        return $this->method;
    }

    /**
     * Returns the value of variableName
     */
    public function getVariableName(): string
    {
        return $this->variableName;
    }

    /**
     * Sets the value of variableName
     *
     * @return  self
     */
    public function setVariableName(string $variableName)
    {
        $this->variableName = $variableName;

        return $this;
    }
}
