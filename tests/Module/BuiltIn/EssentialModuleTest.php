<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Module\BuiltIn;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Definition\RecipeMaker\YamlRecipeMaker;
use RecipeRunner\RecipeRunner\Module\BuiltIn\EssentialModule;
use RecipeRunner\RecipeRunner\Recipe\RecipeParser;
use Yosymfony\Collection\MixedCollection;

class EssentialModuleTest extends TestCase
{
    /** @var RecipeParser */
    private $recipeParser;

    /** @var YamlRecipeMaker */
    private $recipeMaker;
    
    public function setUp(): void
    {
        $this->recipeMaker = new YamlRecipeMaker();
        $this->recipeParser = RecipeParser::Create(new MixedCollection([new EssentialModule()]));
    }

    public function testMethodRegisterVariableMustRegisterVariables(): void
    {
        $ymlRecipe = <<<'yaml'
steps:
    - actions:
        - register_variables:
            user: "victor"
          register: my_variables
yaml;
        $recipeVariables = new MixedCollection();
        $recipeDefinition = $this->recipeMaker->makeRecipeFromString($ymlRecipe);

        $this->recipeParser->parse($recipeDefinition, $recipeVariables);

        $this->assertEquals('victor', $recipeVariables->getDot('my_variables.user'));
    }

    /**
    * @expectedException InvalidArgumentException
    * @expectedExceptionMessage No variables have been declared at method "register_variables".
    */
    public function testMethodRegisterVariableMustFailWhenThereIsNoVariables(): void
    {
        $ymlRecipe = <<<'yaml'
steps:
    - actions:
        - register_variables:
          register: my_variables
yaml;
        $recipeVariables = new MixedCollection();
        $recipeDefinition = $this->recipeMaker->makeRecipeFromString($ymlRecipe);

        $this->recipeParser->parse($recipeDefinition, $recipeVariables);
    }
}
