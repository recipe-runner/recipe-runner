<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Module;

use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Module\ModuleBase;
use Yosymfony\Collection\CollectionInterface;

/**
 * Fake module let you create a module using callables.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class FakeModule extends ModuleBase
{
    /**
     * Runs a method. This method is part of ModuleInterface.
     *
     * @param Method $method
     * @param CollectionInterface $recipeVariables
     *
     * @return ExecutionResult The result of the execution.
     */
    public function runMethod(Method $method, CollectionInterface $recipeVariables) : ExecutionResult
    {
        return $this->runInternalMethod($method, $recipeVariables);
    }

    /**
     * Adds a new method to the fake module.
     *
     * @param string $name Name of the method.
     * @param callable Handler of the method. It must return an ExecutionResult.
     *
     * @return void
     */
    public function addMethod(string $name, callable $handler): void
    {
        $this->addMethodHandler($name, $handler);
    }
}
