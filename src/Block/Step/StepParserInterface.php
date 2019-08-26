<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Block\Step;

use RecipeRunner\RecipeRunner\Definition\StepDefinition;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use Yosymfony\Collection\CollectionInterface;

/**
 * Interface for step parsers.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
interface StepParserInterface
{
    /**
     * Parses a step definition.
     *
     * @param StepDefinition $step
     * @param RecipeVariablesContainer $recipeVariables
     *
     * @return CollectionInterface List of BlockResults from both step and step's actions.
     */
    public function parse(StepDefinition $step, RecipeVariablesContainer $recipeVariables): CollectionInterface;
}
