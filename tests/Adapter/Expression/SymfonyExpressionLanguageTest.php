<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Test\Adapter\Expression;

use PHPUnit\Framework\TestCase;
use Yosymfony\Collection\MixedCollection;
use RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;

class SymfonyExpressionLanguageTest extends TestCase
{
    /** @var SymfonyExpressionLanguage */
    private $expressionResolver;

    /** @var MixedCollection */
    private $variables;

    public function setUp() : void
    {
        $this->variables = new MixedCollection();
        $this->expressionResolver = new SymfonyExpressionLanguage();
    }

    public function testResolveStringInterpolationMustReturnTheSameStringWhenThereAreNotPlaceHolders() : void
    {
        $literal = 'hi victor';
        $resolved = $this->expressionResolver->resolveStringInterpolation($literal, $this->variables);

        $this->assertEquals($literal, $resolved);
    }

    public function testResolveStringInterpolationMustReturnAStringWithPlaceHolderResolved() : void
    {
        $this->variables->add('name', 'Victor');
        $literal = 'hi {{name}}, the sum is {{ 1 + 1 }}';
        $resolved = $this->expressionResolver->resolveStringInterpolation($literal, $this->variables);

        $this->assertEquals('hi Victor, the sum is 2', $resolved);
    }

    public function testResolveStringInterpolationMustReturnNumberOneWhenAPlaceHolderIsResolvedToTrue() : void
    {
        $literal = '{{ true }}';
        $resolved = $this->expressionResolver->resolveStringInterpolation($literal, $this->variables);

        $this->assertEquals('1', $resolved);
    }

    public function testResolveStringInterpolationMustReturnEmptyStringWhenAPlaceHolderIsResolvedToFalse() : void
    {
        $literal = '{{ false }}';
        $resolved = $this->expressionResolver->resolveStringInterpolation($literal, $this->variables);

        $this->assertEquals('', $resolved);
    }

    public function testResolveStringInterpolationMustReturnAStringWithPlaceHolderNotResoledWhenItConsistsInJustWitheSpaces() : void
    {
        $literal = 'hi {{ }}';
        $resolved = $this->expressionResolver->resolveStringInterpolation($literal, $this->variables);

        $this->assertEquals('hi {{ }}', $resolved);
    }

    public function testResolveStringInterpolationMustReturnAStringWithPlaceHolderNotResolvedWhenItIsEmpty() : void
    {
        $literal = 'hi {{}}';
        $resolved = $this->expressionResolver->resolveStringInterpolation($literal, $this->variables);

        $this->assertEquals('hi {{}}', $resolved);
    }

    /**
     * @expectedException RecipeRunner\Expression\Exception\ErrorResolvingExpressionException
     * @expectedExceptionMessage List are not valid values for string interpolation. Expression: "1..4". Literal: "numbers {{1..4}}".
     */
    public function testNameResolveStringInterpolationMustFailWhenPlaceHolderIsResolvedToAList() : void
    {
        $literal = 'numbers {{1..4}}';
        $resolved = $this->expressionResolver->resolveStringInterpolation($literal, $this->variables);
    }

    /**
     * @expectedException RecipeRunner\Expression\Exception\ErrorResolvingExpressionException
     * @expectedExceptionMessage Error resolving expression: "1..".
     */
    public function testNameResolveStringInterpolationMustFailWhenBadExpression() : void
    {
        $literal = 'numbers {{1..}}';
        $resolved = $this->expressionResolver->resolveStringInterpolation($literal, $this->variables);
    }
}
