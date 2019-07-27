<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test;

use PHPUnit\Framework\TestCase;
use Yosymfony\Collection\MixedCollection;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;

class RecipeVariablesContainerTest extends TestCase
{
    /** @var RecipeVariablesContainer */
    private $recipeVariables;

    public function setUp(): void
    {
        $this->recipeVariables = new RecipeVariablesContainer(new MixedCollection(['name' => 'Víctor']));
    }
    
    public function testGetRecipeVariablesMustReturnRecipeVariables() : void
    {
        $this->assertTrue($this->recipeVariables->getRecipeVariables()->has('name'));
        $this->assertEquals('Víctor', $this->recipeVariables->getRecipeVariables()->get('name'));
    }

    public function testRegisterRecipeVariableMustRegisterANewVariableInRegisteredBucket() : void
    {
        $this->recipeVariables->registerRecipeVariable('country', 'Spain');

        $this->assertEquals([
            'name' => 'Víctor',
            'registered' => [
                'country' => 'Spain',
            ],
        ], $this->recipeVariables->getRecipeVariables()->toArray());
    }

    public function testMakeWithScopeVariablesMustReturnAContaiterWithTheScopeVariables() : void
    {
        $scopeVariables = $this->recipeVariables->makeWithScopeVariables(new MixedCollection(['country' => 'Spain']));

        $this->assertEquals([
            'name' => 'Víctor',
            'country' => 'Spain',
            'registered' => [],
        ], $scopeVariables->getScopeVariables()->toArray());
    }
}
