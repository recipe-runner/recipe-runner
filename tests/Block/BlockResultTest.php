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
use RecipeRunner\RecipeRunner\Block\BlockResult;
use RecipeRunner\RecipeRunner\Block\IterationResult;
use Yosymfony\Collection\MixedCollection;

class BlockResultTest extends TestCase
{
    public function testHasErrorMustReturnTrueIfAnyIterationResultIsNotSuccessful(): void
    {
        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
            new IterationResult(true, false),
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);

        $this->assertTrue($blockResult->hasError());
    }

    public function testHasErrorMustReturnFalseWhenAllIterationResultsAreSuccessful(): void
    {
        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
            new IterationResult(true, true),
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);

        $this->assertFalse($blockResult->hasError());
    }

    public function testGetNumberOfIterationsMustReturnTheNumberOfIteration(): void
    {
        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
            new IterationResult(true, true),
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);

        $this->assertEquals(2, $blockResult->getNumberOfIterations());
    }

    public function testGetIterationAtMustReturnTheIterationAtTheIndex(): void
    {
        $iterationResultExpected = new IterationResult(true, false);

        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
            $iterationResultExpected,
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);

        $this->assertEquals($iterationResultExpected, $blockResult->getIterationResultAt(1));
    }

    /**
    * @expectedException InvalidArgumentException
    * @expectedExceptionMessage Iteration index must be a positive value starting at zero.
    */
    public function testGetIterationAtMustFailWhenTheIndexIsSubZero(): void
    {
        $iterationResults = new MixedCollection();
        $blockResult = new BlockResult('a1', $iterationResults);

        $blockResult->getIterationResultAt(-1);
    }

    /**
    * @expectedException InvalidArgumentException
    * @expectedExceptionMessage The iteration index "1" is out of range.
    */
    public function testGetIterationAtMustFailWhenTheIndexIsOutOfRange(): void
    {
        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);

        $blockResult->getIterationResultAt(1);
    }

    public function testGetBlockIdMustReturnTheIdOfTheBlockThatGeneratedTheResult(): void
    {
        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);

        $this->assertEquals('a1', $blockResult->getBlockId());
    }

    public function testSetParentBlockData(): void
    {
        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);
        $blockResult->setParentBlockData('a0', 0);

        $this->assertEquals('a0', $blockResult->getParentBlockId());
        $this->assertEquals(0, $blockResult->getParentIterationNumber());
    }

    /**
    * @expectedException InvalidArgumentException
    * @expectedExceptionMessage Iteration number must be a positive value based on zero.
    */
    public function testSetParentBlockDataMustFailWhenNegativeIterationNumber(): void
    {
        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);
        $blockResult->setParentBlockData('a0', -1);
    }

    /**
    * @expectedException InvalidArgumentException
    * @expectedExceptionMessage Invalid identifier. Only non-empty values are allowed.
    */
    public function testSetParentBlockDataMustFailWhenInvalidParentId(): void
    {
        $iterationResults = new MixedCollection([
            new IterationResult(true, true),
        ]);
        $blockResult = new BlockResult('a1', $iterationResults);
        $blockResult->setParentBlockData('', 0);
    }

    /**
    * @expectedException InvalidArgumentException
    * @expectedExceptionMessage Invalid identifier. Only non-empty values are allowed.
    */
    public function testConstructorMustFailWhenInvalidId(): void
    {
        $iterationResults = new MixedCollection([
        ]);
        $blockResult = new BlockResult('', $iterationResults);
    }
}
