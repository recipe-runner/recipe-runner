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

use RecipeRunner\RecipeRunner\Definition\RecipeDefinition;
use Yosymfony\Collection\CollectionInterface;

/**
 * Interface for recipe parsers.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
interface RecipeParserInterface
{
    /**
     * Parses a recipe definition.
     *
     * @param RecipeDefinition $recipe The recipe definition.
     * @param CollectionInterface $recipeVariables Collection of variables available during the process.
     *
     * @return CollectionInterface List of BlockResult from steps and actions.
     */
    public function parse(RecipeDefinition $recipe, CollectionInterface $recipeVariables): CollectionInterface;
}
