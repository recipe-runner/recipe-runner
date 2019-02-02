<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Module\Invocation;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Module\Invocation\ExecutionResult;

class ExecutionResultTest extends TestCase
{
    public function testGetJsonResultMustReturnAJsonString() : void
    {
        $executionResult = new ExecutionResult('{}');

        $this->assertEquals('{}', $executionResult->getJsonResult());
    }

    public function testIsSuccessIsTrueByDefault() : void
    {
        $executionResult = new ExecutionResult('{}');

        $this->assertTrue($executionResult->isSuccess());
    }

    public function testIsSuccessMustReturnTrueWhenItWasSetToThatValue() : void
    {
        $executionResult = new ExecutionResult('{}', true);

        $this->assertTrue($executionResult->isSuccess());
    }

    public function testIsSuccessMustReturnFalseWhenItWasSetToThatValue() : void
    {
        $executionResult = new ExecutionResult('{}', false);

        $this->assertFalse($executionResult->isSuccess());
    }
}
