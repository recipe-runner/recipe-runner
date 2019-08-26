<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Block;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Block\IterationResult;

class IterationResultTest extends TestCase
{
    public function testIsSkippedMustBeTrueWhenStatusIsSkipped(): void
    {
        $result = new IterationResult(IterationResult::STATUS_SKIPPED);

        $this->assertTrue($result->isSkipped());
        $this->assertFalse($result->isSuccessful());
    }

    public function testIsSuccessfulMustBeTrueWhenStatusIsSuccessful(): void
    {
        $result = new IterationResult(IterationResult::STATUS_SUCCESSFUL);

        $this->assertTrue($result->isSuccessful());
        $this->assertFalse($result->isSkipped());
    }

    public function testStatusErrorIsNotSuccessfulNorSkipped(): void
    {
        $result = new IterationResult(IterationResult::STATUS_ERROR);

        $this->assertTrue($result->isFailed());
        $this->assertFalse($result->isSuccessful());
        $this->assertFalse($result->isSkipped());
    }

    /**
     * @testWith    [-1]
     *              [3]
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid iteration status.
     */
    public function testMustFailWhenInvalidStatus(int $invalidStatus): void
    {
        $result = new IterationResult($invalidStatus);
    }
}
