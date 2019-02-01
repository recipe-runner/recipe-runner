<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Definition;

use Yosymfony\Collection\CollectionInterface;

class RecipeDefinition
{
    private $name;
    
    /** @var CollectionInterface */
    private $stepDefinitions;

    public function __construct(string $name, CollectionInterface $stepDefinitions)
    {
        if (\strlen(\trim($name)) == 0) {
            throw new InvalidArgumentException('The parameter name cannot be empty.');
        }

        if ($stepDefinitions->isEmpty()) {
            throw new InvalidArgumentException('A recipe needs at least one step.');
        }

        $this->name = $name;
        $this->stepDefinitions = $stepDefinitions;
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Returns the list of steps that compose this recipe.
     *
     * @return StepDefinition[]
     */
    public function getStepDefinitions(): CollectionInterface
    {
        return $this->stepDefinitions;
    }
}
