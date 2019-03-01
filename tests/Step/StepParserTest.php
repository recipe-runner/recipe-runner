<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Step;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Action\ActionParser;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use RecipeRunner\RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\RecipeRunner\Definition\StepDefinition;
use RecipeRunner\RecipeRunner\IO\NullIO;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use RecipeRunner\RecipeRunner\Step\StepParser;
use RecipeRunner\RecipeRunner\Test\Module\FakeModule;
use Yosymfony\Collection\MixedCollection;

class StepParserTest extends TestCase
{
    /** @var RecipeVariablesContainer */
    private $recipeVariables;

    /** @var StepParser */
    private $stepParser;

    public function setUp(): void
    {
        $modules = new MixedCollection([$this->createFakeModule()]);
        $expressionResolver = new SymfonyExpressionLanguage();
        $moduleExecutor = new ModuleMethodExecutor($modules, $expressionResolver, new NullIO());
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

    public function testSecondActionMustHaveAccessToTheVariableRegisteredByTheFirstStep(): void
    {
        $method1 = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $method2 = $this->createMethodInvocation('hi_you', ['name' => 'The name was "{{previous["message"]}}"']);
        $step = $this->createStepWithTwoActions($method1, $method2, 'previous', 'final_message');

        $result = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertEquals('Hi The name was "Hi Víctor"', $this->recipeVariables->getRecipeVariables()->getDot('final_message.message'));
    }

    private function createStepWithAction(Method $method, ?string $registerVariable = null): StepDefinition
    {
        $action = new ActionDefinition('test action', $method);

        if ($registerVariable !== null) {
            $action->setVariableName($registerVariable);
        }

        return new StepDefinition('test step', new MixedCollection([$action]));
    }

    private function createStepWithTwoActions(Method $method1, Method $method2, ?string $registerVariable1 = null, ?string $registerVariable2 = null): StepDefinition
    {
        $action1 = new ActionDefinition('test action 1', $method1);
        $action2 = new ActionDefinition('test action 2', $method2);

        if ($registerVariable1 !== null) {
            $action1->setVariableName($registerVariable1);
        }

        if ($registerVariable2 !== null) {
            $action2->setVariableName($registerVariable2);
        }

        return new StepDefinition('test step', new MixedCollection([$action1, $action2]));
    }

    private function createMethodInvocation($name, array $parameters = []): Method
    {
        $method = new Method($name);

        foreach ($parameters as $name => $value) {
            $method->addParameter($name, $value);
        }

        return $method;
    }

    private function createFakeModule(): FakeModule
    {
        $module = new FakeModule('TestModule');
        $module->addMethod('hi_you', function (Method $method) {
            $name = $method->getParameterNameOrPosition('name', 0);
            
            return \json_encode(['message' => "Hi {$name}"]);
        });

        return $module;
    }
}
