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
use Yosymfony\Collection\MixedCollection;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;

class ModuleMethodExecutorTest extends TestCase
{
    /** @var ModuleMethodExecutor */
    private $moduleMethodExecutor;

    public function setUp() : void
    {
        $this->moduleMethodExecutor = new ModuleMethodExecutor(new MixedCollection([$this->createFakeModule()]));
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
        $module->setExpressionResolver(new SymfonyExpressionLanguage());
        $module->addMethod('hi_you', function (Method $method) {
            $name = $method->getParameterNameOrPosition('name', 0);

            return \json_encode(['message' => "Hi {$name}"]);
        });

        return $module;
    }
}
