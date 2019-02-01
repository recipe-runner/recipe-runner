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

use InvalidArgumentException;
use Yosymfony\Collection\CollectionInterface;
use RecipeRunner\Module\Invocation\Method;
use RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\Module\Exception\MethodNotFoundException;

class ModuleMethodExecutor
{
    /** @var MixedCollection */
    private $modules;

    /**
     * Constructor.
     *
     * @param ModuleInterface[] $modules List of modules.
     */
    public function __construct(CollectionInterface $modules)
    {
        $this->validateModuleCollection($modules);
        $this->modules = $modules;
    }

    /**
     * Runs a method of any registered module.
     *
     * @return string JSON with the result.
     */
    public function runMethod(Method $method, CollectionInterface $recipeVariables) : ExecutionResult
    {
        foreach ($this->modules as $module) {
            if ($module->checkMethod($method)) {
                return $module->runMethod($method, $recipeVariables);
            }
        }

        throw new MethodNotFoundException("Method \"{$method->getName()}\" not found. Maybe there is a missing module.", $method);
    }

    private function validateModuleCollection(CollectionInterface $modules) : void
    {
        $isValid = $modules->every(function ($module) {
            return $module instanceof ModuleInterface;
        });

        if (!$isValid) {
            $message = 'Invalid module collection. Some elements are not implementing ModuleInterface.';
            throw new InvalidArgumentException($message);
        }
    }
}
