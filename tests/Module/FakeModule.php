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

use RecipeRunner\RecipeRunner\Module\ModuleBase;
use Yosymfony\Collection\MixedCollection;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;

class FakeModule extends ModuleBase
{
    private $name;
    private $version;

    public function __construct(string $name, string $version = '0.0.0')
    {
        parent::__construct();

        $this->name = $name;
        $this->version = $version;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getVersion() : string
    {
        return $this->version;
    }

    public function runMethod(Method $method, MixedCollection $recipeVariables) : ExecutionResult
    {
        $result = $this->runInternalMethod($method, $recipeVariables);

        return new ExecutionResult($result);
    }

    public function addMethod(string $name, callable $handler)
    {
        $this->addMethodHandler($name, $handler);
    }
}
