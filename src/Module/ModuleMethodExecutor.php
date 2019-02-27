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

use InvalidArgumentException;
use RecipeRunner\RecipeRunner\Expression\ExpressionResolverInterface;
use RecipeRunner\RecipeRunner\IO\IOInterface;
use RecipeRunner\RecipeRunner\Module\Exception\MethodNotFoundException;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use Yosymfony\Collection\CollectionInterface;

class ModuleMethodExecutor
{
    /** @var CollectionInterface */
    private $modules;

    /**
     * Constructor.
     *
     * @param ModuleInterface[] $modules List of modules.
     */
    public function __construct(CollectionInterface $modules, ExpressionResolverInterface $expressionResolver, IOInterface $io)
    {
        $this->validateModuleCollection($modules);
        $this->setUpModules($modules, $expressionResolver, $io);
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

    private function setUpModules(CollectionInterface $modules, ExpressionResolverInterface $expressionResolver, IOInterface $io)
    {
        foreach ($modules as $module) {
            $module->setExpressionResolver($expressionResolver);
            $module->setIO($io);
        }
    }
}
