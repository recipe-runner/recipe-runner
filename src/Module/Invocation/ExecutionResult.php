<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Module\Invocation;

final class ExecutionResult
{
    private $jsonResult;
    private $success;

    public function __construct(string $jsonResult, bool $success = true)
    {
        $this->jsonResult = $jsonResult;
        $this->success = $success;
    }

    public function getJsonResult() : string
    {
        return $this->jsonResult;
    }

    public function isSuccess() : bool
    {
        return $this->success;
    }
}
