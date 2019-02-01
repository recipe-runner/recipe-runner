<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Test;

use PHPUnit\Framework\TestCase;
use Yosymfony\Collection\MixedCollection;
use RecipeRunner\RecipeVariablesContainer;

class RecipeVariablesContainerTest extends TestCase
{
    public function testGetRecipeVariablesMustReturnRecipeVariables() : void
    {
        $recipeVariables = new RecipeVariablesContainer(new MixedCollection(['name' => 'Víctor']));

        $this->assertEquals([
            'name' => 'Víctor',
        ], $recipeVariables->getRecipeVariables()->toArray());
    }

    public function testRegisterRecipeVariableMustRegisterANewVariable() : void
    {
        $recipeVariables = new RecipeVariablesContainer(new MixedCollection(['name' => 'Víctor']));
        $recipeVariables->registerRecipeVariable('country', 'Spain');

        $this->assertEquals([
            'name' => 'Víctor',
            'country' => 'Spain',
        ], $recipeVariables->getRecipeVariables()->toArray());
    }

    public function testMakeWithScopeVariablesMustReturnAContaiterWithTheScopeVariables() : void
    {
        $recipeVariables = new RecipeVariablesContainer(new MixedCollection(['name' => 'Víctor']));
        $scopeVariables = $recipeVariables->makeWithScopeVariables(new MixedCollection(['country' => 'Spain']));

        $this->assertEquals([
            'name' => 'Víctor',
            'country' => 'Spain',
        ], $scopeVariables->getScopeVariables()->toArray());

        $this->assertEquals([
            'name' => 'Víctor',
        ], $scopeVariables->getRecipeVariables()->toArray());
    }
}
