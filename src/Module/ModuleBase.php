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
use RecipeRunner\IO\IOTrait;
use RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\Module\Invocation\Method;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

/**
 * Base class for modules providing run and check method.
 */
abstract class ModuleBase implements ModuleInterface
{
    use IOTrait;
    
    /** @var MixedCollection */
    protected $methods;

    /** @var ExpressionResolverInterface */
    protected $expressionResolver;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->methods = new MixedCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function checkMethod(Method $method) : bool
    {
        return $this->methods->has($method->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function setExpressionResolver(ExpressionResolverInterface $expressionResolver) : void
    {
        $this->expressionResolver = $expressionResolver;
    }

    /**
     * Runs a register method.
     *
     * @return mixed
     */
    protected function runInternalMethod(Method $method, CollectionInterface $recipeVariables)
    {
        $methodParameters = $method->getParameters();
        $methodHandler = $this->methods->get($method->getName());

        return $methodHandler($this->resolveMethodPlaceholders($method, $recipeVariables));
    }

    /**
     * Register a new method handler.
     *
     * @param string $methodName Name of the method.
     * @param callable $handler Handler for the method.
     *
     * @return void
     */
    protected function addMethodHandler(string $methodName, callable $handler) : void
    {
        $this->methods->add($methodName, $handler);
    }

    /**
     * Returns a new method with the parameter placeholders resolved.
     *
     * @param Method $method
     * @param CollectionInterface $recipeVariables
     *
     * @return Method
     */
    protected function resolveMethodPlaceholders(Method $method, CollectionInterface $recipeVariables): Method
    {
        $resolvedMethod = new Method($method->getName());

        foreach ($method->getParameters() as $name => $value) {
            $resolvedMethod->addParameter($name, $this->resolvePlaceholdersIfString($value, $recipeVariables));
        }

        return $resolvedMethod;
    }

    private function resolvePlaceholdersIfString($value, CollectionInterface $recipeVariables)
    {
        if (\is_string($value) === false || $this->expressionResolver === null) {
            return $value;
        }
        
        return $this->expressionResolver->resolveStringInterpolation($value, $recipeVariables);
    }
}
