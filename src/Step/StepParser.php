<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Step;

use RecipeRunner\Action\ActionParser;
use RecipeRunner\Definition\StepDefinition;
use RecipeRunner\Expression\ExpressionResolverInterface;
use RecipeRunner\ParserBase;
use RecipeRunner\RecipeVariablesContainer;
use RecipeRunner\Step\StepResult;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class StepParser extends ParserBase
{
    /** @var ActionParser */
    private $actionParser;

    /**
     * Constructor.
     *
     * @param ActionParser $actionParser
     * @param ExpressionResolverInterface $expressionResolver
     */
    public function __construct(ActionParser $actionParser, ExpressionResolverInterface $expressionResolver)
    {
        parent::__construct($expressionResolver);

        $this->actionParser = $actionParser;
    }

    /**
     * @return StepResult[]
     */
    public function parse(StepDefinition $step, RecipeVariablesContainer $recipeVariables): CollectionInterface
    {
        $this->getIO()->write("Parsing step: \"{$step->getName()}\".");

        $loopExpression = $step->getLoopExpression();

        if ($loopExpression == '') {
            $stepResult = $this->runBlock($step, $recipeVariables);

            return new MixedCollection([$stepResult]);
        }

        $loopItems = $this->evaluateLoopExpressionIfItIsString($loopExpression, $recipeVariables->getScopeVariables());
        
        return $this->runBlockInLoop($step, $loopItems, $recipeVariables);
    }

    private function runBlock(StepDefinition $step, RecipeVariablesContainer $recipeVariables) : StepResult
    {
        if (!$this->evaluateWhenCondition($step->getWhenExpression(), $recipeVariables->getScopeVariables())) {
            return new StepResult(true, false, $step);
        }

        list($actionResults, $succeed) = $this->runAllActions($step, $recipeVariables);

        return new StepResult($succeed, true, $step, $actionResults);
    }

    /**
     * @return StepResult[]
     */
    private function runBlockInLoop(StepDefinition $step, CollectionInterface $loopItems, RecipeVariablesContainer $recipeVariables): CollectionInterface
    {
        $stepResults = new MixedCollection();

        foreach ($loopItems as $key => $value) {
            $blockVariablesContainer = $recipeVariables->makeWithScopeVariables($this->generateLoopVariables($key, $value, 'step_loop'));

            $stepResults->add($key, $this->runBlock($step, $blockVariablesContainer));
        }

        return $stepResults;
    }

    private function runAllActions(StepDefinition $step, RecipeVariablesContainer $recipeVariables): array
    {
        $succeed = true;
        $actionResults = new MixedCollection();
        
        foreach ($step->getActionDefinitions() as $action) {
            $actionResult = $this->actionParser->parse($action, $recipeVariables);
            
            if (!$actionResult->firstOrDefault()->getSucceed()) {
                $succeed = false;
            }

            $actionResults->set($action->getName(), $actionResult);
        }

        return [
            $actionResults,
            $succeed,
        ];
    }
}
