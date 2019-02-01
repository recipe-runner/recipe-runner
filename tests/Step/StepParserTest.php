<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Test\Step;

use PHPUnit\Framework\TestCase;
use RecipeRunner\Action\ActionParser;
use RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\Definition\StepDefinition;
use RecipeRunner\Module\Invocation\Method;
use RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeVariablesContainer;
use RecipeRunner\Step\StepParser;
use RecipeRunner\Test\Module\FakeModule;
use Yosymfony\Collection\MixedCollection;

class StepParserTest extends TestCase
{
    /** @var RecipeVariablesContainer */
    private $recipeVariables;

    /** @var StepParser */
    private $stepParser;

    public function setUp(): void
    {
        $expressionResolver = new SymfonyExpressionLanguage();
        $moduleExecutor = new ModuleMethodExecutor(new MixedCollection([$this->createFakeModule($expressionResolver)]));
        $actionParser = new ActionParser($expressionResolver, $moduleExecutor);

        $this->stepParser = new StepParser($actionParser, $expressionResolver);
        $this->recipeVariables = new RecipeVariablesContainer(new MixedCollection());
    }

    public function testParseMustParseAStepWithoutWhereCondition(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $step = $this->createStepWithAction($method);
        
        $result = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertFalse($result->isEmpty());
        $this->assertTrue($result->firstOrDefault()->getSucceed());
        $this->assertTrue($result->firstOrDefault()->getExecuted());
    }

    public function testParseMustParseAStepWithTrueWhereCondition(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $step = $this->createStepWithAction($method);
        $step->setWhenExpression('true');
        
        $result = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertFalse($result->isEmpty());
        $this->assertTrue($result->firstOrDefault()->getSucceed());
        $this->assertTrue($result->firstOrDefault()->getExecuted());
    }

    public function testParseMustNotParseAStepWithFalseWhereCondition(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $step = $this->createStepWithAction($method);
        $step->setWhenExpression('false');
        
        $result = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertFalse($result->isEmpty());
        $this->assertTrue($result->firstOrDefault()->getSucceed());
        $this->assertFalse($result->firstOrDefault()->getExecuted());
    }

    public function testParseMustExecuteAllTheActionsOfAStepAsMuchTimesAsElementsHasTheLoopExpression(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $step = $this->createStepWithAction($method);
        $step->setLoopExpression('[1,2]');
        
        $result = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertCount(2, $result);
    }

    public function testParseMustExposeLoopVariablesWhenALoopExpressionIsSet(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor #{{step_loop["value"]}} index: {{step_loop["index"]}}']);
        $step = $this->createStepWithAction($method, 'greetings');
        $step->setLoopExpression('[1]');
        
        $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertEquals([
            'greetings' => [
                'message' => 'Hi Víctor #1 index: 0',
                'success' => true,
            ],
        ], $this->recipeVariables->getRecipeVariables()->toArray());
    }

    public function testParseMustConsiderLoopVariablesInWhereExpression(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor #{{step_loop["value"]}} index: {{step_loop["index"]}}']);
        $step = $this->createStepWithAction($method);
        $step->setLoopExpression('[1,2]');
        $step->setWhenExpression('step_loop["value"] == 2');
        
        $result = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertCount(2, $result);
        $this->assertFalse($result->firstOrDefault()->getExecuted());
        $this->assertTrue($result->lastOrDefault()->getExecuted());
    }

    private function createStepWithAction(Method $method, ?string $registerVariable = null): StepDefinition
    {
        $action = new ActionDefinition('test action', $method);

        if ($registerVariable !== null) {
            $action->setVariableName($registerVariable);
        }

        return new StepDefinition('test step', new MixedCollection([$action]));
    }

    private function createMethodInvocation($name, array $parameters = []): Method
    {
        $method = new Method($name);

        foreach ($parameters as $name => $value) {
            $method->addParameter($name, $value);
        }

        return $method;
    }

    private function createFakeModule(SymfonyExpressionLanguage $expressionResolver): FakeModule
    {
        $module = new FakeModule('TestModule');
        $module->setExpressionResolver($expressionResolver);
        $module->addMethod('hi_you', function (Method $method) {
            $name = $method->getParameterNameOrPosition('name', 0);
            
            return \json_encode(['message' => "Hi {$name}"]);
        });

        return $module;
    }
}
