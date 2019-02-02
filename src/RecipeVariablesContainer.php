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
    /** @var CollectionInterface */
    private $recipeVariables;

    /** @var CollectionInterface */
    private $scopeVariables;

    /** @var CollectionInterface */
    private $mixedVariables;

    public function __construct(CollectionInterface $recipeVariables)
    {
        $this->recipeVariables = $recipeVariables;
        $this->scopeVariables = new MixedCollection();
    }

    public function getRecipeVariables() : CollectionInterface
    {
        return new MixedCollection($this->recipeVariables->all());
    }

    public function registerRecipeVariable($name, $content) : void
    {
        $this->recipeVariables->set($name, $content);
    }

    public function getScopeVariables() : CollectionInterface
    {
        if ($this->mixedVariables === null) {
            $this->mixedVariables = $this->recipeVariables->union($this->scopeVariables)->getReadOnlyCollection();
        }

        return new MixedCollection($this->mixedVariables->all());
    }

    public function makeWithScopeVariables(CollectionInterface $variables) : RecipeVariablesContainer
    {
        $container = new RecipeVariablesContainer($this->recipeVariables);
        $container->scopeVariables = $variables->union($this->scopeVariables);

        return $container;
    }
}
