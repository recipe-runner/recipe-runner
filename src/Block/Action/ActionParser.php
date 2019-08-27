<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Block\Action;

use RecipeRunner\RecipeRunner\Block\Action\Exception\InvalidJsonException;
use RecipeRunner\RecipeRunner\Block\BlockCommonOperation;
use RecipeRunner\RecipeRunner\Block\BlockResult;
use RecipeRunner\RecipeRunner\Block\IterationResult;
use RecipeRunner\RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

/**
 * Parser for actions.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
final class ActionParser implements ActionParserInterface
{
    /** @var BlockCommonOperation */
    private $blockCommonOperation;

    /** @var ModuleMethodExecutor */
    private $methodExecutor;

    /** @var string */
    private $actionName;

    /**
     * Constructor.
     *
     * @param BlockCommonOperation $blockCommonOperation
     * @param ModuleMethodExecutor $methodExecutor
     */
    public function __construct(BlockCommonOperation $blockCommonOperation, ModuleMethodExecutor $methodExecutor)
    {
        $this->blockCommonOperation = $blockCommonOperation;
        $this->methodExecutor = $methodExecutor;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidJsonException If the method execution returns an invalid JSON.
     */
    public function parse(ActionDefinition $action, RecipeVariablesContainer $recipeVariables): BlockResult
    {
        $this->actionName = $action->getName();
        $loopExpression = $action->getLoopExpression();

        if ($loopExpression == '') {
            $iterationResult = $this->runBlock($action, $recipeVariables);

            return new BlockResult($action->getId(), new MixedCollection([$iterationResult]));
        }

        $loopItems = $this->blockCommonOperation->evaluateLoopExpressionIfItIsString($loopExpression, $recipeVariables->getScopeVariables());
        
        $iterationResults = $this->runBlockInLoop($action, $loopItems, $recipeVariables);

        return new BlockResult($action->getId(), new MixedCollection($iterationResults));
    }

    private function runBlock(ActionDefinition $action, RecipeVariablesContainer $recipeVariables) : IterationResult
    {
        if (!$this->blockCommonOperation->evaluateWhenCondition($action->getWhenExpression(), $recipeVariables->getScopeVariables())) {
            return new IterationResult(IterationResult::STATUS_SKIPPED);
        }

        $executionMethodResult = $this->methodExecutor->runMethod($action->getMethod(), $recipeVariables->getScopeVariables());

        $this->registerVariableIfNecessary($recipeVariables, $action, $this->generateMethodExecutionResultVariables($executionMethodResult));

        $status = $executionMethodResult->isSuccess() ? IterationResult::STATUS_SUCCESSFUL : IterationResult::STATUS_FAILED;
        return new IterationResult($status);
    }

    /**
     * @return IterationResult[]
     */
    private function runBlockInLoop(ActionDefinition $action, CollectionInterface $loopItems, RecipeVariablesContainer $recipeVariables) : array
    {
        $recipeIterationVariables = new MixedCollection();
        $iterationResults = [];

        foreach ($loopItems as $key => $value) {
            $blockVariablesContainer = $recipeVariables->makeWithScopeVariables($this->blockCommonOperation->generateLoopVariables($key, $value));
            $scopeVariables = $blockVariablesContainer->getScopeVariables();
            
            if ($this->blockCommonOperation->evaluateWhenCondition($action->getWhenExpression(), $scopeVariables)) {
                $executionMethodResult = $this->methodExecutor->runMethod($action->getMethod(), $scopeVariables);
                $recipeIterationVariables->add($key, $this->generateMethodExecutionResultVariables($executionMethodResult));
                $status = $executionMethodResult->isSuccess() ? IterationResult::STATUS_SUCCESSFUL : IterationResult::STATUS_FAILED;
                $iterationResults[] = new IterationResult($status);
            } else {
                $iterationResults[] = new IterationResult(IterationResult::STATUS_SKIPPED); //new IterationResult(false, true);
            }
        }

        $this->registerVariableIfNecessary($recipeVariables, $action, $recipeIterationVariables);

        return $iterationResults;
    }

    private function getVariablesFromMethodExecutionResultJson(ExecutionResult $result) : MixedCollection
    {
        $json = $result->getJsonResult();
        $variables = \json_decode($json, true);

        if ($variables === null) {
            $message = "Error parsing the JSON string returned by the method in the action \"{$this->actionName}\".";
            throw new InvalidJsonException($message, $json);
        }

        return new MixedCollection($variables);
    }

    private function generateMethodExecutionResultVariables(ExecutionResult $executionResult) : CollectionInterface
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
