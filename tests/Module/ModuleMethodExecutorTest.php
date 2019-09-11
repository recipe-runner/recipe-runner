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

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use RecipeRunner\RecipeRunner\IO\NullIO;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use Yosymfony\Collection\MixedCollection;

class ModuleMethodExecutorTest extends TestCase
{
    /** @var ModuleMethodExecutor */
    private $moduleMethodExecutor;

    public function setUp() : void
    {
        $io = new NullIO();
        $modules = new MixedCollection([$this->createFakeModule()]);
        $expressionResolver =  new SymfonyExpressionLanguage();
        $this->moduleMethodExecutor = new ModuleMethodExecutor($modules, $expressionResolver, $io);
    }

    public function testRunMethodShouldRunAMethod() : void
    {
        $recipeVariables = new MixedCollection();
        $method = $this->createMethodInvocation('hi_you', ['victor']);
        
        $executionResult = $this->moduleMethodExecutor->runMethod($method, $recipeVariables);
        $result = \json_decode($executionResult->getJsonResult(), true);

        $this->assertEquals('Hi victor', $result['message']);
    }

    /**
     * @expectedException RecipeRunner\RecipeRunner\Module\Exception\MethodNotFoundException
     * @expectedExceptionMessage Method "foo" not found. Maybe there is a missing module.
     */
    public function testRunMethodMustFailWhenMethodNotFound() : void
    {
        $recipeVariables = new MixedCollection();
        $method = $this->createMethodInvocation('foo');
        
        $this->moduleMethodExecutor->runMethod($method, $recipeVariables);
    }

    private function createMethodInvocation($name, array $parameters = []) : Method
    {
        $method = new Method($name);

        foreach ($parameters as $name => $value) {
            $method->addParameter($name, $value);
        }

        return $method;
    }

    private function createFakeModule() : FakeModule
    {
        $module = new FakeModule('TestModule');
        $module->addMethod('hi_you', function (Method $method) {
            $name = $method->getParameterNameOrPosition('name', 0);

            return new ExecutionResult(\json_encode(['message' => "Hi {$name}"]), true);
        });

        return $module;
    }
}
