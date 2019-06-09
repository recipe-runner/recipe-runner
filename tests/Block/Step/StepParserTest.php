<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Block\Step;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use RecipeRunner\RecipeRunner\Block\Action\ActionParser;
use RecipeRunner\RecipeRunner\Block\BlockCommonOperation;
use RecipeRunner\RecipeRunner\Block\BlockResult;
use RecipeRunner\RecipeRunner\Block\Step\StepParser;
use RecipeRunner\RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\RecipeRunner\Definition\StepDefinition;
use RecipeRunner\RecipeRunner\IO\NullIO;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use RecipeRunner\RecipeRunner\Test\Module\FakeModule;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class StepParserTest extends TestCase
{
    /** @var RecipeVariablesContainer */
    private $recipeVariables;

    /** @var StepParser */
    private $stepParser;

    private $actionId1 = 'action1';
    private $actionId2 = 'action2';
    private $stepId1 = 'step1';

    public function setUp(): void
    {
        $modules = new MixedCollection([$this->createFakeModule()]);
        $expressionResolver = new SymfonyExpressionLanguage();
        $moduleExecutor = new ModuleMethodExecutor($modules, $expressionResolver, new NullIO());
        $blockCommonOperation = new BlockCommonOperation($expressionResolver);
        $actionParser = new ActionParser($blockCommonOperation, $moduleExecutor);

        $this->stepParser = new StepParser($actionParser, $blockCommonOperation);
        $this->recipeVariables = new RecipeVariablesContainer(new MixedCollection());
    }

    public function testParseMustParseAStepWithoutWhereCondition(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $step = $this->createStepWithAction($method);
        
        $blocResultCollection = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertCount(2, $blocResultCollection, 'Failed asserting that there are 2 block results: one from the step and one the action.');

        $StepBlockResult = $this->getFirstBlockId($this->stepId1, $blocResultCollection);
        
        $this->assertTrue($StepBlockResult->getIterationResultAt(0)->isExecuted());
        $this->assertTrue($StepBlockResult->getIterationResultAt(0)->isSuccessful());
    }

    public function testParseMustParseAStepWithTrueWhereCondition(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $step = $this->createStepWithAction($method);
        $step->setWhenExpression('true');

        $blocResultCollection = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertCount(2, $blocResultCollection, 'Failed asserting that there are 2 block results: one from the step and one the action.');

        $StepBlockResult = $this->getFirstBlockId($this->stepId1, $blocResultCollection);
        
        $this->assertTrue($StepBlockResult->getIterationResultAt(0)->isExecuted());
        $this->assertTrue($StepBlockResult->getIterationResultAt(0)->isSuccessful());
    }

    public function testParseMustNotParseAStepWithFalseWhereCondition(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $step = $this->createStepWithAction($method);
        $step->setWhenExpression('false');

        $blocResultCollection = $this->stepParser->parse($step, $this->recipeVariables);
        $this->assertCount(1, $blocResultCollection, 'Failed asserting that there is only 1 block results: the one from the step.');

        $StepBlockResult = $this->getFirstBlockId($this->stepId1, $blocResultCollection);
        
        $this->assertFalse($StepBlockResult->getIterationResultAt(0)->isExecuted());
        $this->assertTrue($StepBlockResult->getIterationResultAt(0)->isSuccessful());
    }

    public function testParseMustExecuteAllTheActionsOfAStepAsMuchTimesAsElementsHasTheLoopExpression(): void
    {
        $method = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $step = $this->createStepWithAction($method);
        $step->setLoopExpression('[1,2]');

        $blocResultCollection = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertCount(3, $blocResultCollection, 'Failed asserting that there are 3 block results: one from the step and two from the actions.');
    }

    public function testParseMustExposeLoopVariablesToActionsWhenALoopExpressionIsSet(): void
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

        $blocResultCollection = $this->stepParser->parse($step, $this->recipeVariables);
        $this->assertCount(2, $blocResultCollection, 'Failed asserting that there are 2 block results: one from the step and one from the action executed.');
        
        $StepBlockResult = $this->getFirstBlockId($this->stepId1, $blocResultCollection);
        
        $this->assertFalse($StepBlockResult->getIterationResultAt(0)->isExecuted());
        $this->assertTrue($StepBlockResult->getIterationResultAt(1)->isExecuted());
    }

    public function testSecondActionMustHaveAccessToTheVariableRegisteredByTheFirstActionOfTheStep(): void
    {
        $method1 = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $method2 = $this->createMethodInvocation('hi_you', ['name' => 'The name was "{{previous["message"]}}"']);
        $step = $this->createStepWithTwoActions($method1, $method2, 'previous', 'final_message');

        $result = $this->stepParser->parse($step, $this->recipeVariables);
        $finalMessage = $this->recipeVariables->getRecipeVariables()->getDot('final_message.message');

        $this->assertEquals('Hi The name was "Hi Víctor"', $finalMessage);
    }

    public function testParseMustReturnAListOfBlockResultsOneFromStepAlongWithBlockResultsFromItsActions(): void
    {
        $method1 = $this->createMethodInvocation('hi_you', ['name' => 'Víctor']);
        $method2 = $this->createMethodInvocation('hi_you', ['name' => 'Jack']);
        $step = $this->createStepWithTwoActions($method1, $method2);

        $blockResults = $this->stepParser->parse($step, $this->recipeVariables);

        $this->assertContainsOnlyInstancesOf(BlockResult::class, $blockResults);
        $this->assertCount(3, $blockResults);
    }

    private function createStepWithAction(Method $method, ?string $registerVariable = null): StepDefinition
    {
        $action = new ActionDefinition($this->actionId1, $method);

        if ($registerVariable !== null) {
            $action->setVariableName($registerVariable);
        }

        return new StepDefinition($this->stepId1, new MixedCollection([$action]));
    }

    private function createStepWithTwoActions(Method $method1, Method $method2, ?string $registerVariable1 = null, ?string $registerVariable2 = null): StepDefinition
    {
        $action1 = new ActionDefinition($this->actionId1, $method1);
        $action2 = new ActionDefinition($this->actionId2, $method2);

        if ($registerVariable1 !== null) {
            $action1->setVariableName($registerVariable1);
        }

        if ($registerVariable2 !== null) {
            $action2->setVariableName($registerVariable2);
        }

        return new StepDefinition($this->stepId1, new MixedCollection([$action1, $action2]));
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

    private function getFirstBlockId(string $id, CollectionInterface $collection): BlockResult
    {
        $result = $collection->where(function (BlockResult $item) use ($id) {
            return $item->getBlockId() == $id;
        });

        return $result->firstOrDefault();
    }
}
