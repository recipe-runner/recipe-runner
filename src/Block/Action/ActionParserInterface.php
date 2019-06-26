<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Block\Action;

use RecipeRunner\RecipeRunner\Block\BlockResult;
use RecipeRunner\RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;

/**
 * Interface for action parsers.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
interface ActionParserInterface
{
    /**
     * Parses an action definition.
     *
     * @param ActionDefinition $action
     * @param RecipeVariablesContainer $recipeVariables
     *
     * @return BlockResult
     */
    public function parse(ActionDefinition $action, RecipeVariablesContainer $recipeVariables): BlockResult;
}
