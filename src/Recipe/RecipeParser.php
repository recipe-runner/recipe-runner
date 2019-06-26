<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Recipe;

use RecipeRunner\RecipeRunner\Block\Step\StepParserInterface;
use RecipeRunner\RecipeRunner\Definition\RecipeDefinition;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class RecipeParser
{
    /** @var StepParser */
    private $stepParser;

    /**
     * Constructor.
     *
     * @param StepParser $stepParser The step parser.
     */
    public function __construct(StepParserInterface $stepParser)
    {
        $this->stepParser = $stepParser;
    }

    /**
     * Parses a recipe definition.
     *
     * @param RecipeDefinition $recipe The recipe definition.
     * @param CollectionInterface $recipeVariables Collection of variables available during the process.
     *
     * @return BlockResult[] List of block result from steps and actions.
     */
    public function parse(RecipeDefinition $recipe, CollectionInterface $recipeVariables): CollectionInterface
    {
        $blockResults = [];

        $stepResults = new MixedCollection();
        $recipeVariablesContainer = new RecipeVariablesContainer($recipeVariables->copy());

        foreach ($recipe->getStepDefinitions() as $step) {
            $stepBlockCollection = $this->stepParser->parse($step, $recipeVariablesContainer);
            $blockResults = \array_merge($blockResults, $stepBlockCollection->all());
        }

        return new MixedCollection($blockResults);
    }
}
