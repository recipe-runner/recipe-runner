<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Module\Invocation;

use InvalidArgumentException;
use Yosymfony\Collection\MixedCollection;

class Method
{
    private $name;

    /** @var MixedCollection */
    private $parameters;

    /**
     * Constructor.
     *
     * @param string $name Name of the method.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->parameters = new MixedCollection();
    }

    /**
     * Returns the name of the method.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Adds a new parameter.
     *
     * @param string $name Name of the parameter.
     * @param mixed $value Value of the parameter.
     *
     * @return Method
     *
     * @throws InvalidArgumentException If the parameter was added previously.
     */
    public function addParameter(string $name, $value) : Method
    {
        if ($this->parameters->has($name)) {
            throw new InvalidArgumentException("The parameter {$name} has been added previously.");
        }

        $this->parameters->add($name, $value);

        return $this;
    }

    /**
     * Returns the collection of parameters with the parameter name as key.
     *
     * @return MixedCollection
     */
    public function getParameters() : MixedCollection
    {
        return $this->parameters->copy();
    }

    /**
     * Returns the parameter associated with the name or the position passed.
     *
     * @param string $name Parameter name
     * @param int $position 0-indexed position.
     * @param mixed $default Default value in case parameter is not found.
     *
     * @return mixed
     */
    public function getParameterNameOrPosition(string $name, int $position, $default = null)
    {
        if ($this->parameters->has($name)) {
            return $this->parameters[$name];
        }

        $currentPosition = 0;

        foreach ($this->parameters as $value) {
            if ($currentPosition == $position) {
                return $value;
            }

            $currentPosition++;
        }

        return $default;
    }
}
