<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Block\Step;

use RecipeRunner\RecipeRunner\Block\Action\ActionParserInterface;
use RecipeRunner\RecipeRunner\Block\BlockCommonOperation;
use RecipeRunner\RecipeRunner\Block\BlockResult;
use RecipeRunner\RecipeRunner\Block\IterationResult;
use RecipeRunner\RecipeRunner\Block\Step\StepResult;
use RecipeRunner\RecipeRunner\Definition\StepDefinition;
use RecipeRunner\RecipeRunner\RecipeVariablesContainer;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

/**
 * Parser for steps.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
final class StepParser implements StepParserInterface
{
    /** @var BlockCommonOperation */
    private $blockCommonOperation;

    /** @var ActionParserInterface */
    private $actionParser;

    /** @var BlockResult[] */
    private $blockResults = [];

    /**
     * Constructor.
     *
     * @param ActionParserInterface $actionParser
     * @param BlockCommonOperation $blockCommonOperation
     */
    public function __construct(ActionParserInterface $actionParser, BlockCommonOperation $blockCommonOperation)
    {
        $this->actionParser = $actionParser;
        $this->blockCommonOperation = $blockCommonOperation;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(StepDefinition $step, RecipeVariablesContainer $recipeVariables): CollectionInterface
    {
        $this->blockResults = [];
        $loopExpression = $step->getLoopExpression();

        if ($loopExpression == '') {
            $iterationResult = $this->runBlock($step, $recipeVariables, 0);

            $this->blockResults[] = new BlockResult($step->getId(), new MixedCollection([$iterationResult]));

            return new MixedCollection($this->blockResults);
        }

        $loopItems = $this->blockCommonOperation->evaluateLoopExpressionIfItIsString($loopExpression, $recipeVariables->getScopeVariables());
        
        $iterationResults = $this->runBlockInLoop($step, $loopItems, $recipeVariables);

        $this->blockResults[] = new BlockResult($step->getId(), new MixedCollection($iterationResults));

        return new MixedCollection($this->blockResults);
    }

    private function runBlock(StepDefinition $step, RecipeVariablesContainer $recipeVariables, int $iterationNumber) : IterationResult
    {
        if (!$this->blockCommonOperation->evaluateWhenCondition($step->getWhenExpression(), $recipeVariables->getScopeVariables())) {
            return new IterationResult(IterationResult::STATUS_SKIPPED);
        }

        $isSuccessful = $this->runAllActions($step, $recipeVariables, $iterationNumber);
        $status = $isSuccessful ? IterationResult::STATUS_SUCCESSFUL : IterationResult::STATUS_FAILED;

        return new IterationResult($status);
    }

    /**
     * @return IterationResult[]
     */
    private function runBlockInLoop(StepDefinition $step, CollectionInterface $loopItems, RecipeVariablesContainer $recipeVariables): array
    {
        $iterationResults = [];
        $iterationNumber = 0;

        foreach ($loopItems as $key => $value) {
            $blockVariablesContainer = $recipeVariables->makeWithScopeVariables($this->blockCommonOperation->generateLoopVariables($key, $value, 'step_loop'));

            $iterationResults[] = $this->runBlock($step, $blockVariablesContainer, $iterationNumber++);
        }

        return $iterationResults;
    }

    private function runAllActions(StepDefinition $step, RecipeVariablesContainer $recipeVariables, int $iterationNumber): bool
    {
        $isSuccessful = true;
        
        foreach ($step->getActionDefinitions() as $action) {
            $actionBlockResult = $this->actionParser->parse($action, $recipeVariables);
            $actionBlockResult->setParentBlockData($step->getId(), $iterationNumber);

            if ($actionBlockResult->isFailed()) {
                $isSuccessful = false;
            }

            $this->blockResults[] = $actionBlockResult;
        }

        return $isSuccessful;
    }
}
