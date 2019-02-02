<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Step;

use RecipeRunner\RecipeRunner\Definition\StepDefinition;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class StepResult
{
    private $stepDefinition;
    private $actionResults;
    private $executed;
    private $succeed;

    public function __construct(bool $succeed, bool $executed, StepDefinition $stepDefinition, CollectionInterface $actionResults = null)
    {
        $this->executed = $executed;
        $this->succeed = $succeed;
        $this->stepDefinition = $stepDefinition;
        $this->actionResults = $actionResults ?? new MixedCollection();
    }

    /**
     * Indicates if the task was executed.
     *
     * @return bool
     */
    public function getExecuted(): bool
    {
        return $this->executed;
    }

    /**
     * Indicates if the step was executed without any error.
     * If the step was not executed this value will be true.
     *
     * @return bool
     */
    public function getSucceed(): bool
    {
        return $this->succeed;
    }

    /**
     * Returns the action results.
     *
     * @return ActionResult[]
     */
    public function getActionResults(): CollectionInterface
    {
        return $this->actionResults;
    }

    /**
     * Returns the step definition.
     *
     * @return StepDefinition
     */
    public function getStepDefinition(): StepDefinition
    {
        return $this->stepDefinition;
    }
}
