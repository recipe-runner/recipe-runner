<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Module;

use RecipeRunner\Expression\ExpressionResolverInterface;
use RecipeRunner\IO\IOAwareInterface;
use RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\Module\Invocation\Method;
use Yosymfony\Collection\MixedCollection;

/**
 * Interface for all modules.
 */
interface ModuleInterface extends IOAwareInterface
{
    /**
     * Returns the name of the module.
     */
    public function getName() : string;

    /**
     * Returns the version of the module.
     */
    public function getVersion() : string;

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
     * @param MixedCollection $recipeVariables
     *
     * @return string Result of the execution in JSON format.
     */
    public function runMethod(Method $method, MixedCollection $recipeVariables) : ExecutionResult;

    /**
     * Sets the expression resolver.
     *
     * @param ExpressionResolverInterface $expressionResolver
     */
    public function setExpressionResolver(ExpressionResolverInterface $expressionResolver) : void;
}
