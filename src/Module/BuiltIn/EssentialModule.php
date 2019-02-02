<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Module\BuiltIn;

use InvalidArgumentException;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Module\ModuleBase;
use Yosymfony\Collection\MixedCollection;

class EssentialModule extends ModuleBase
{
    private const NAME = 'Essential';
    private const VERSION = '0.0.0.0';

    public function __construct()
    {
        parent::__construct();

        $this->addMethodHandler('register_variables', [$this, 'registerVariableMethod']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion() : string
    {
        return self::VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function runMethod(Method $method, MixedCollection $recipeVariables) : ExecutionResult
    {
        return $this->runInternalMethod($method, $recipeVariables);
    }

    public function registerVariableMethod(Method $method): ExecutionResult
    {
        $variableCollection = $method->getParameters();
        
        if ($variableCollection->isEmpty()) {
            throw new InvalidArgumentException("No variables have been declared at method \"{$method->getName()}\".");
        }
        
        return new ExecutionResult($variableCollection->toJson());
    }
}
