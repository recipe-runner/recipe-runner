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
use RecipeRunner\RecipeRunner\Block\BlockCommonOperation;
use RecipeRunner\RecipeRunner\Expression\ExpressionResolverInterface;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class BlockCommonOperationTest extends TestCase
{
    /** @var ExpressionResolverInterface */
    private $expressionResolverMock;

    /** @var BlockCommonOperation */
    private $blockCommonOperation;

    public function setUp(): void
    {
        $this->expressionResolverMock = $this->createMock(ExpressionResolverInterface::class);
        $this->blockCommonOperation = new BlockCommonOperation($this->expressionResolverMock);
    }

    public function testMustGenerateLoopVariablesWithTheDefaultRootItemName(): void
    {
        $result = $this->blockCommonOperation->generateLoopVariables(0, 'test');

        $this->assertEquals([
            'loop' => [
                'index' => 0,
                'value' => 'test',
            ],
        ], $result->toArray());
    }

    public function testMustGenerateLoopVariablesWithTheRootItemNameSet(): void
    {
        $result = $this->blockCommonOperation->generateLoopVariables(0, 'test', 'rootItemName');

        $this->assertEquals([
            'rootItemName' => [
                'index' => 0,
                'value' => 'test',
            ],
        ], $result->toArray());
    }

    public function testEvaluateWhenConditionMustReturnTrueWhenEmptyExpression(): void
    {
        $recipeVariables = $this->createMock(CollectionInterface::class);
        $emptyExpression = '';

        $this->assertTrue($this->blockCommonOperation->evaluateWhenCondition($emptyExpression, $recipeVariables));
    }

    public function testEvaluateWhenConditionMustCallExpressionResolverWhenNotEmptyExpression(): void
    {
        $this->expressionResolverMock->expects($this->once())
            ->method('resolveBooleanExpression')
            ->with($this->equalTo('1=1'))
            ->willReturn(true);

        $recipeVariables = $this->createMock(CollectionInterface::class);
        $emptyExpression = '1=1';

        $this->assertTrue($this->blockCommonOperation->evaluateWhenCondition($emptyExpression, $recipeVariables));
    }

    public function testEvaluateLoopExpressionIfItIsStringMustReturnTheCollectionIfTheLoopExpressionIsACollection(): void
    {
        $recipeVariables = $this->createMock(CollectionInterface::class);
        $collectionExpression = $this->createMock(CollectionInterface::class);

        $result = $this->blockCommonOperation->evaluateLoopExpressionIfItIsString($collectionExpression, $recipeVariables);

        $this->assertInstanceOf(CollectionInterface::class, $result);
    }

    public function testEvaluateLoopExpressionIfItIsStringMustReturnACollectionIfTheLoopExpressionIsAString(): void
    {
        $listAsString = '[1,2]';
        $listAsArray = [1,2];
        $recipeVariables = $this->createMock(CollectionInterface::class);
        $this->expressionResolverMock->expects($this->once())
            ->method('resolveCollectionExpression')
            ->with($this->equalTo($listAsString))
            ->willReturn(new MixedCollection($listAsArray));

        $result = $this->blockCommonOperation->evaluateLoopExpressionIfItIsString($listAsString, $recipeVariables);

        $this->assertEquals($listAsArray, $result->toArray());
    }
}
