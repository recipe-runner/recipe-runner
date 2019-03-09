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

class FakeModule extends ModuleBase
{
    public function runMethod(Method $method, CollectionInterface $recipeVariables) : ExecutionResult
    {
        $result = $this->runInternalMethod($method, $recipeVariables);

        return new ExecutionResult($result);
    }

    public function addMethod(string $name, callable $handler)
    {
        $this->addMethodHandler($name, $handler);
    }
}
