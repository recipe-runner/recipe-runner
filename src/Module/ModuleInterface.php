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
use RecipeRunner\RecipeRunner\IO\IOInterface;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use Yosymfony\Collection\CollectionInterface;

/**
 * Interface for all modules.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
interface ModuleInterface
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
     * @return ExecutionResult
     */
    public function runMethod(Method $method, CollectionInterface $recipeVariables) : ExecutionResult;

    /**
     * Sets the expression resolver.
     *
     * @param ExpressionResolverInterface $expressionResolver
     */
    public function setExpressionResolver(ExpressionResolverInterface $expressionResolver) : void;

    /**
     * Returns the IO instance.
     *
     * @return IOInterface;
     */
    public function getIO(): IOInterface;

    /**
     * Sets the IO.
     *
     * @param IOInterface $io
     *
     * @return void
     */
    public function setIO(IOInterface $io): void;
}
