<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Recipe;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Block\BlockResult;
use RecipeRunner\RecipeRunner\Block\Step\StepParserInterface;
use RecipeRunner\RecipeRunner\Definition\RecipeDefinition;
use RecipeRunner\RecipeRunner\Definition\StepDefinition;
use RecipeRunner\RecipeRunner\Recipe\RecipeParser;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use Yosymfony\Collection\MixedCollection;

class RecipeParserTest extends TestCase
{
    public function testRunRecipeMustExecuteTheRecipe() : void
    {
        $recipeVariables = new MixedCollection();
        $blockResultMock = $this->getMockBuilder(BlockResult::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stepDefMock = $this->getMockBuilder(StepDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stepParserMock = $this->getMockBuilder(StepParserInterface::class)
            ->setMethods(['parse'])
            ->getMock();
        $stepParserMock->expects($this->once())
            ->method('parse')
            ->with(
                $this->equalTo($stepDefMock),
                $this->equalTo(new RecipeVariablesContainer($recipeVariables))
            )
            ->willReturn(new MixedCollection());
        $recipeParser = new RecipeParser($stepParserMock);

        $recipe = new RecipeDefinition('test recipe', new MixedCollection([$stepDefMock]));

        $recipeParser->parse($recipe, $recipeVariables);
    }

    public function testRunRecipeMustReturnAListOfBlockResults() : void
    {
        $recipeVariables = new MixedCollection();
        $blockResultMock = $this->getMockBuilder(BlockResult::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stepDefMock = $this->getMockBuilder(StepDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stepParserMock = $this->getMockBuilder(StepParserInterface::class)
            ->setMethods(['parse'])
            ->getMock();
        $stepParserMock->expects($this->once())
            ->method('parse')
            ->with(
                $this->equalTo($stepDefMock),
                $this->equalTo(new RecipeVariablesContainer($recipeVariables))
            )
            ->willReturn(new MixedCollection([$blockResultMock]));
        $recipeParser = new RecipeParser($stepParserMock);

        $recipe = new RecipeDefinition('test recipe', new MixedCollection([$stepDefMock]));

        $blockResults = $recipeParser->parse($recipe, $recipeVariables);

        $this->assertContainsOnlyInstancesOf(BlockResult::class, $blockResults);
    }
}
