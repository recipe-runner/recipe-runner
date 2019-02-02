<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Action;

use RecipeRunner\RecipeRunner\Action\Exception\InvalidJsonException;
use RecipeRunner\RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\RecipeRunner\Expression\ExpressionResolverInterface;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeRunner\ParserBase;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class ActionParser extends ParserBase
{
    /** @var ModuleMethodExecutor */
    private $methodExecutor;

    /**
     * Constructor.
     *
     * @param ExpressionResolverInterface $expressionResolver
     * @param ModuleMethodExecutor $methodExecutor
     */
    public function __construct(ExpressionResolverInterface $expressionResolver, ModuleMethodExecutor $methodExecutor)
    {
        parent::__construct($expressionResolver);

        $this->methodExecutor = $methodExecutor;
    }

    /**
     * @return ActionResult[]
     */
    public function parse(ActionDefinition $action, RecipeVariablesContainer $recipeVariables): CollectionInterface
    {
        $this->getIO()->write("Parsing action: \"{$action->getName()}\".");

        $loopExpression = $action->getLoopExpression();

        if ($loopExpression == '') {
            $actionResult = $this->runBlock($action, $recipeVariables);

            return new MixedCollection([$actionResult]);
        }

        $loopItems = $this->evaluateLoopExpressionIfItIsString($loopExpression, $recipeVariables->getScopeVariables());
        
        return $this->runBlockInLoop($action, $loopItems, $recipeVariables);
    }

    private function runBlock(ActionDefinition $action, RecipeVariablesContainer $recipeVariables) : ActionResult
    {
        if (!$this->evaluateWhenCondition($action->getWhenExpression(), $recipeVariables->getScopeVariables())) {
            return new ActionResult(true, false, $action);
        }

        $executionMethodResult = $this->methodExecutor->runMethod($action->getMethod(), $recipeVariables->getScopeVariables());

        $this->registerVariableIfNecessary($recipeVariables, $action, $this->generateMethodExecutionResultVariables($executionMethodResult));

        return new ActionResult($executionMethodResult->isSuccess(), true, $action);
    }

    /**
     * @return ActionResult[]
     */
    private function runBlockInLoop(ActionDefinition $action, CollectionInterface $loopItems, RecipeVariablesContainer $recipeVariables) : CollectionInterface
    {
        $recipeIterationVariables = new MixedCollection();
        $actionResults = new MixedCollection();

        foreach ($loopItems as $key => $value) {
            $blockVariablesContainer = $recipeVariables->makeWithScopeVariables($this->generateLoopVariables($key, $value));
            $scopeVariables = $blockVariablesContainer->getScopeVariables();
            
            if ($this->evaluateWhenCondition($action->getWhenExpression(), $scopeVariables)) {
                $executionMethodResult = $this->methodExecutor->runMethod($action->getMethod(), $scopeVariables);
                $recipeIterationVariables->add($key, $this->generateMethodExecutionResultVariables($executionMethodResult));
                $actionResults->add($key, new ActionResult($executionMethodResult->isSuccess(), true, $action));
            } else {
                $actionResults->add($key, new ActionResult(true, false, $action));
            }
        }

        $this->registerVariableIfNecessary($recipeVariables, $action, $recipeIterationVariables);

        return $actionResults;
    }

    private function getVariablesFromMethodExecutionResultJson(ExecutionResult $result) : MixedCollection
    {
        $json = $result->getJsonResult();
        $variables = \json_decode($json, true);

        if ($variables === null) {
            $message = "Error parsing a JSON string.";
            throw new InvalidJsonException($message, $json);
        }

        return new MixedCollection($variables);
    }

    private function generateMethodExecutionResultVariables(ExecutionResult $executionResult) : MixedCollection
    {
        $result = (new MixedCollection([
            'success' => $executionResult->isSuccess(),
        ]))->union($this->getVariablesFromMethodExecutionResultJson($executionResult));

        return $result;
    }

    private function registerVariableIfNecessary(RecipeVariablesContainer $recipeVariables, ActionDefinition $action, $value): void
    {
        $variableName = $action->getVariableName();

        if ($variableName != '') {
            $recipeVariables->registerRecipeVariable($variableName, $value);
        }
    }
}
