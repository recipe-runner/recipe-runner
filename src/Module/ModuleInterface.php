<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Module;

use RecipeRunner\RecipeRunner\Expression\ExpressionResolverInterface;
use RecipeRunner\RecipeRunner\IO\IOAwareInterface;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use Yosymfony\Collection\CollectionInterface;

/**
 * Interface for all modules.
 */
interface ModuleInterface extends IOAwareInterface
{
    /**
     * Checks if the method belong to a module.
     *
     * @return bool
     */
    public function checkMethod(Method $method) : bool;

    /**
     * Runs a method of a module.
     *
     * @param Method $method The method to be executed.
     * @param CollectionInterface $recipeVariables
     *
     * @return string Result of the execution in JSON format.
     */
    public function runMethod(Method $method, CollectionInterface $recipeVariables) : ExecutionResult;

    /**
     * Sets the expression resolver.
     *
     * @param ExpressionResolverInterface $expressionResolver
     */
    public function setExpressionResolver(ExpressionResolverInterface $expressionResolver) : void;
}
