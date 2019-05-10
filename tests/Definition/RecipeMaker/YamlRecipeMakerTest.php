<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Definition\RecipeMaker;

use PHPUnit\Framework\TestCase;

use RecipeRunner\RecipeRunner\Definition\RecipeMaker\YamlRecipeMaker;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;

class YamlRecipeMakerTest extends TestCase
{
    public function testMakeRecipeFromStringMustReturnARecipeDefinition() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps:
    - name: "step 1"
      actions:
        - name: "Directory {{ current_dir }}"
          shell: "ls -a"
          register: "currentDir"
          when: os_family == "Ubuntu"
          loop:
            - "item1"
            - "item2"
      when: 1==0
yaml;
        $method = new Method('shell');
        $method->addParameter(0, 'ls -a');
        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);

        $this->assertEquals('My first recipe', $recipeDefinition->getName());
        
        $stepDefinitions = $recipeDefinition->getStepDefinitions();

        $this->assertCount(1, $stepDefinitions);
        $this->assertEquals('step 1', $stepDefinitions->firstOrDefault()->getName());
        $this->assertEquals('1==0', $stepDefinitions->firstOrDefault()->getWhenExpression());

        $actionDefinitions = $stepDefinitions->firstOrDefault()->getActionDefinitions();

        $this->assertCount(1, $actionDefinitions);
        $this->assertEquals('Directory {{ current_dir }}', $actionDefinitions[0]->getName());
        $this->assertCount(2, $actionDefinitions[0]->getLoopExpression());
        $this->assertEquals('os_family == "Ubuntu"', $actionDefinitions[0]->getWhenExpression());
        $this->assertEquals($method, $actionDefinitions[0]->getMethod());
        $this->assertTrue($recipeDefinition->getExtra()->isEmpty());
    }

    public function testMakeRecipeFromStringMustReturnExtraData(): void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
extra:
    rr:
      stop_on_error: false
steps:
    - name: "step 1"
      actions:
        - name: "Directory {{ current_dir }}"
          shell: "ls -a"
yaml;
        $method = new Method('shell');
        $method->addParameter(0, 'ls -a');
        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);

        $this->assertEquals([
                'rr' => [
                  'stop_on_error' => false,
                ],
              ], $recipeDefinition->getExtra()->toArray());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid schema: "when" has to be a string expression.
     */
    public function testMakeRecipeFromStringMustFailWhenAStepWhenExpressionIsANonString() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps:
    - name: "step 1"
      actions:
        - shell: "ls -a"
      when: 1
yaml;

        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid schema: "when" has to be a string expression.
     */
    public function testMakeRecipeFromStringMustFailWhenAnActionWhenExpressionIsANonString() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps:
    - name: "step 1"
      actions:
        - shell: "ls -a"
          when: 1
yaml;

        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid schema: "loop" has to be a string expression or a list of items.
     */
    public function testMakeRecipeFromStringMustFailWhenAStepLoopExpressionIsANonStringOrList() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps:
    - name: "step 1"
      actions:
        - shell: "ls -a"
      loop: 1
yaml;

        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid schema: "loop" has to be a string expression or a list of items.
     */
    public function testMakeRecipeFromStringMustFailWhenAnActionLoopExpressionIsANonStringOrList() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps:
    - name: "step 1"
      actions:
        - shell: "ls -a"
          loop: 1
yaml;

        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid schema: "register" has to be a string value.
     */
    public function testMakeRecipeFromStringMustFailWhenAnActionRegisterIsANonString() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps:
    - name: "step 1"
      actions:
        - shell: "ls -a"
          register: 1
yaml;

        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid schema: "steps" has to be an array value.
     */
    public function testMakeRecipeFromStringMustFailWhenStepsNodeIsANonArray() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps: true
yaml;

        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid schema: "actions" has to be an array value.
     */
    public function testMakeRecipeFromStringMustFailWhenActionsNodeIsANonArray() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps:
    - actions: true
yaml;

        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid schema: an action needs one and just one invocation method.
     */
    public function testMakeRecipeFromStringMustFailWhenThereIsNonActionMethod() : void
    {
        $ymlRecipe = <<<'yaml'
name: "My first recipe"
steps:
    - name: "step 1"
      actions:
        - name: "action 1"
          when: true
yaml;

        $maker = new YamlRecipeMaker();
        $recipeDefinition = $maker->makeRecipeFromString($ymlRecipe);
    }
}
