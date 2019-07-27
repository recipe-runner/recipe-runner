<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner;

use Yosymfony\Collection\MixedCollection;
use Yosymfony\Collection\CollectionInterface;

class RecipeVariablesContainer
{
    private const REGISTERED_VARIABLES_ITEM = 'registered';

    /** @var CollectionInterface */
    private $recipeVariables;

    /** @var CollectionInterface */
    private $scopeVariables;
    
    /** @var CollectionInterface */
    private $registeredVariables;

    public function __construct(CollectionInterface $recipeVariables)
    {
        $this->recipeVariables = $recipeVariables;
        $this->scopeVariables = new MixedCollection();
        $this->registeredVariables = new MixedCollection();
    }

    public function getRecipeVariables() : CollectionInterface
    {
        $recipeVariables = $this->recipeVariables->copy();
        $this->addRegisteredVariablesItem($recipeVariables);

        return $recipeVariables;
    }

    public function registerRecipeVariable($name, $content) : void
    {
        $this->registeredVariables[$name] = $content;
    }

    public function getScopeVariables() : CollectionInterface
    {
        $scopeVariables = $this->recipeVariables->union($this->scopeVariables);
        $this->addRegisteredVariablesItem($scopeVariables);

        return $scopeVariables;
    }

    public function makeWithScopeVariables(CollectionInterface $variables) : RecipeVariablesContainer
    {
        $container = new RecipeVariablesContainer($this->recipeVariables);
        $container->registeredVariables = $this->registeredVariables;
        $container->scopeVariables = $variables->union($this->scopeVariables);

        return $container;
    }

    private function addRegisteredVariablesItem(CollectionInterface $collection): void
    {
        $collection->add(self::REGISTERED_VARIABLES_ITEM, $this->registeredVariables);
    }
}
