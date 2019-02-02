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

use RecipeRunner\RecipeRunner\Definition\ActionDefinition;

class ActionResult
{
    private $action;
    private $executed;
    private $succeed;

    public function __construct(bool $succeed, bool $executed, ActionDefinition $action)
    {
        $this->executed = $executed;
        $this->succeed = $succeed;
        $this->action = $action;
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
     * Indicates if the action was executed without any error.
     * If the action was not executed this value will be true.
     *
     * @return bool
     */
    public function getSucceed(): bool
    {
        return $this->succeed;
    }

    /**
     * Returns the action definition related to the result.
     *
     * @return ActionDefinition
     */
    public function getAction(): ActionDefinition
    {
        return $this->action;
    }
}
