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
use Yosymfony\Collection\MixedCollection;

/**
 * Recipe definition.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class RecipeDefinition
{
    private $name;
    
    /** @var CollectionInterface */
    private $stepDefinitions;

    /** @var CollectionInterface */
    private $extraData;

    public function __construct(string $name, CollectionInterface $stepDefinitions, CollectionInterface $extra = null)
    {
        if (\strlen(\trim($name)) == 0) {
            throw new InvalidArgumentException('The parameter name cannot be empty.');
        }

        if ($stepDefinitions->isEmpty()) {
            throw new InvalidArgumentException('A recipe needs at least one step.');
        }

        $this->name = $name;
        $this->stepDefinitions = $stepDefinitions;
        $this->extraData = $extra ?? new MixedCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Returns the list of steps that compose this recipe.
     *
     * @return CollectionInterface Collection of StepDefinition.
     */
    public function getStepDefinitions(): CollectionInterface
    {
        return $this->stepDefinitions;
    }

    public function getExtra(): CollectionInterface
    {
        return $this->extraData;
    }
}
