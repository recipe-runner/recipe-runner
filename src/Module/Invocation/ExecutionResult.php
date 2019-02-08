<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Module\Invocation;

final class ExecutionResult
{
    public const EMPTY_JSON = '{}';

    private $jsonResult;
    private $success;

    /**
     * Constructor.
     *
     * @param string $jsonResult Json response. Default value: EMPTY_JSON.
     * @param bool $success Indicates if the execution was successful.
     */
    public function __construct(string $jsonResult = self::EMPTY_JSON, bool $success = true)
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
