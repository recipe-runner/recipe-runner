<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Test\Recipe;

use PHPUnit\Framework\TestCase;
use RecipeRunner\Definition\RecipeDefinition;
use RecipeRunner\Definition\StepDefinition;
use RecipeRunner\Recipe\RecipeParser;
use RecipeRunner\RecipeVariablesContainer;
use RecipeRunner\Step\StepParser;
use Yosymfony\Collection\MixedCollection;

class RecipeParserTest extends TestCase
{
    public function testRunRecipeMustExecuteTheRecipe() : void
    {
        $recipeVariables = new MixedCollection();
        $stepDefMock = $this->getMockBuilder(StepDefinition::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $stepParserMock = $this->getMockBuilder(StepParser::class)
                    ->setMethods(['parse'])
                    ->disableOriginalConstructor()
                    ->getMock();
        $stepParserMock->expects($this->once())
                    ->method('parse')
                    ->with(
                        $this->equalTo($stepDefMock),
                        $this->equalTo(new RecipeVariablesContainer($recipeVariables))
                    );
        $recipeParser = new RecipeParser($stepParserMock);

        
        $recipe = new RecipeDefinition('test recipe', new MixedCollection([$stepDefMock]));

        $recipeParser->parse($recipe, $recipeVariables);
    }
}
