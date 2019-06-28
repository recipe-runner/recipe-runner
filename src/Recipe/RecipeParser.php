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

/**
 * Recipe parser.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
final class RecipeParser implements RecipeParserInterface
{
    /** @var StepParser */
    private $stepParser;

    /**
     * Constructor.
     *
     * @param StepParserInterface $stepParser The step parser.
     */
    public function __construct(StepParserInterface $stepParser)
    {
        $this->stepParser = $stepParser;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(RecipeDefinition $recipe, CollectionInterface $recipeVariables): CollectionInterface
    {
        $blockResults = new MixedCollection();
        $recipeVariablesContainer = new RecipeVariablesContainer($recipeVariables->copy());

        foreach ($recipe->getStepDefinitions() as $step) {
            $stepBlockCollection = $this->stepParser->parse($step, $recipeVariablesContainer);
            $blockResults->addRangeOfValues($stepBlockCollection);
        }

        return $blockResults;
    }
}
