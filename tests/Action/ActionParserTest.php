<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Action;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Action\ActionParser;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use RecipeRunner\RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Module\ModuleBase;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use RecipeRunner\RecipeRunner\Test\Module\FakeModule;
use Yosymfony\Collection\MixedCollection;

class ActionParserTest extends TestCase
{
    /** @var RecipeVariablesContainer */
    private $recipeVariables;

    /** @var ActionParser */
    private $actionParser;

    public function setUp(): void
    {
        $expressionResolver = new SymfonyExpressionLanguage();
        $moduleExecutor = new ModuleMethodExecutor(new MixedCollection([$this->createFakeModule($expressionResolver)]));
        $this->actionParser = new ActionParser($expressionResolver, $moduleExecutor);
        $this->recipeVariables = new RecipeVariablesContainer(new MixedCollection());
    }

    public function testParseMustExecuteTheMethodWhithoutWhereCondition(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $action = new ActionDefinition('test action', $method);
        $action->setVariableName('greetings');
        
        $result = $this->actionParser->parse($action, $this->recipeVariables);

        $this->assertEquals('Hi Víctor', $this->recipeVariables->getRecipeVariables()->getDot('greetings.message'));
        $this->assertTrue($result->firstOrDefault()->getExecuted());
    }

    public function testParseMustExecuteTheMethodWhenWhereConditionIsTrue(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $action = new ActionDefinition('test action', $method);
        $action->setWhenExpression('true');
        
        $result = $this->actionParser->parse($action, $this->recipeVariables);

        $this->assertTrue($result->firstOrDefault()->getExecuted());
    }

    public function testParseMustNotExecuteTheMethodWhenWhereConditionIsFalse(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $action = new ActionDefinition('test action', $method);
        $action->setWhenExpression('false');
        
        $result = $this->actionParser->parse($action, $this->recipeVariables);

        $this->assertFalse($result->firstOrDefault()->getExecuted());
    }

    public function testParseMustReturnTheStatusOfTheExecutionWhenRegisterVariableNameIsSet(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $action = new ActionDefinition('test action', $method);
        $action->setVariableName('greetings');
        
        $result = $this->actionParser->parse($action, $this->recipeVariables);

        $this->assertTrue($this->recipeVariables->getRecipeVariables()->getDot('greetings.success'));
        $this->assertTrue($result->firstOrDefault()->getSucceed());
    }

    public function testParseMustExecuteTheMethodSeveralTimesWhenThereIsALoopExpression(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $action = new ActionDefinition('test action', $method);
        $action->setVariableName('greetings_2_times')
            ->setLoopExpression('[1,2]');
        
        $actionResult = $this->actionParser->parse($action, $this->recipeVariables);

        $this->assertCount(2, $actionResult);
        $this->assertEquals([
            'greetings_2_times' => [
                [
                    'message' => 'Hi Víctor',
                    'success' => true,
                ],
                [
                    'message' => 'Hi Víctor',
                    'success' => true,
                ]
            ],
        ], $this->recipeVariables->getRecipeVariables()->toArray());
    }

    public function testParseMustExecuteTheMethodSeveralTimesWhenThereIsCollectionAsLoopExpression(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $action = new ActionDefinition('test action', $method);
        $action->setVariableName('greetings_2_times')
            ->setLoopExpression(new MixedCollection([1,2]));
        
        $actionResult = $this->actionParser->parse($action, $this->recipeVariables);

        $this->assertCount(2, $actionResult);
        $this->assertEquals([
            'greetings_2_times' => [
                [
                    'message' => 'Hi Víctor',
                    'success' => true,
                ],
                [
                    'message' => 'Hi Víctor',
                    'success' => true,
                ]
            ],
        ], $this->recipeVariables->getRecipeVariables()->toArray());
    }

    public function testParseMustExposeLoopVariablesWhenALoopExpressionIsSet(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor #{{loop["value"]}} index: {{loop["index"]}}']);
        $action = new ActionDefinition('test action', $method);
        $action->setVariableName('greetings')
            ->setLoopExpression(new MixedCollection([1]));
        
        $actionResult = $this->actionParser->parse($action, $this->recipeVariables);

        $this->assertEquals([
            'greetings' => [
                [
                    'message' => 'Hi Víctor #1 index: 0',
                    'success' => true,
                ]
            ],
        ], $this->recipeVariables->getRecipeVariables()->toArray());
    }

    public function testParseMustConsiderLoopVariablesInWhenExpression(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor #{{loop["value"]}} index: {{loop["index"]}}']);
        $action = new ActionDefinition('test action', $method);
        $action->setVariableName('greetings')
            ->setLoopExpression(new MixedCollection(['a' => 1, 'b' => 2]))
            ->setWhenExpression('loop["index"] == "b"');
        
        $actionResult = $this->actionParser->parse($action, $this->recipeVariables);

        $this->assertCount(2, $actionResult);
        $this->assertFalse($actionResult->get('a')->getExecuted());
        $this->assertTrue($actionResult->get('b')->getExecuted());
        $this->assertEquals([
            'greetings' => [
                'b' => [
                    'message' => 'Hi Víctor #2 index: b',
                    'success' => true,
                ],
            ],
        ], $this->recipeVariables->getRecipeVariables()->toArray());
    }

    public function testParseMustConsiderRecipeVariablesInMethodArguments(): void
    {
        $recipeVariables = new RecipeVariablesContainer(new MixedCollection(['country' => 'Spain']));
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor from {{country}}']);
        $action = new ActionDefinition('test action', $method);
        $action->setVariableName('greetings');
        
        $actionResult = $this->actionParser->parse($action, $recipeVariables);

        $this->assertEquals('Hi Víctor from Spain', $recipeVariables->getRecipeVariables()->getDot('greetings.message'));
    }

    /**
     * @expectedException RecipeRunner\RecipeRunner\Expression\Exception\ErrorResolvingExpressionException
     */
    public function testParseMustFailWhenWhenExpressionIsNotBoolean(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $action = new ActionDefinition('test action', $method);
        $action->setWhenExpression('1+1');
        
        $this->actionParser->parse($action, $this->recipeVariables);
        
        $action->parse($this->recipeVariables);
    }

    /**
     * @expectedException RecipeRunner\RecipeRunner\Module\Exception\MethodNotFoundException
     * @expectedExceptionMessage Method "foo" not found.
     */
    public function testParseMustFailWhenTheMethodDoesNotExist(): void
    {
        $method = $this->createMethodInvocation('foo');
        $action = new ActionDefinition('test action', $method);
        
        $this->actionParser->parse($action, $this->recipeVariables);
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
};
