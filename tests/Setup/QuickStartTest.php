<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Setup;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\RecipeRunner\Definition\RecipeDefinition;
use RecipeRunner\RecipeRunner\Definition\StepDefinition;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Recipe\RecipeParser;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use RecipeRunner\RecipeRunner\Setup\QuickStart;
use RecipeRunner\RecipeRunner\Step\StepParser;
use RecipeRunner\RecipeRunner\Test\Module\FakeModule;
use Yosymfony\Collection\MixedCollection;

class QuickStartTest extends TestCase
{
    public function testCreateMustCreateAValidRecipeParserWithEssentialModule(): void
    {
        $recipeVariables = new MixedCollection();
        $method = new Method('register_variables');
        $method->addParameter('name', 'Víctor');
        $recipeDefinition = $this->createRecipeDefinition($method);
        $recipeParser = QuickStart::Create();
        
        $blockResultCollection = $recipeParser->parse($recipeDefinition, $recipeVariables);

        $this->assertCount(2, $blockResultCollection);
    }

    public function testCreateMustCreateAValidRecipeParserWithTheModulesPassed(): void
    {
        $recipeVariables = new MixedCollection();
        $method = new Method('hi_you');
        $recipeDefinition = $this->createRecipeDefinition($method);
        $module = $this->createFakeModule();
        $recipeParser = QuickStart::Create(new MixedCollection([$module]));
        
        $stepResults = $recipeParser->parse($recipeDefinition, $recipeVariables);

        $this->assertCount(2, $stepResults);
    }
    
    private function createRecipeDefinition(Method $method)
    {
        $action = new ActionDefinition('action #1', $method);
        $action->setVariableName('result');
        $step = new StepDefinition('step #1', new MixedCollection([$action]));

        return new RecipeDefinition('test recipe', new MixedCollection([$step]));
    }

    private function createFakeModule(): FakeModule
    {
        $module = new FakeModule('TestModule');
        
        $module->addMethod('hi_you', function (Method $method) {
            return new ExecutionResult(ExecutionResult::EMPTY_JSON);
        });

        return $module;
    }
}
