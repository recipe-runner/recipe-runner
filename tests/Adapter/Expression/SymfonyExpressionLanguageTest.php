<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Adapter\Expression;

use PHPUnit\Framework\TestCase;
use Yosymfony\Collection\MixedCollection;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;

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

    public function testResolveExpressionMustReturnTheResultOfExpressionWhenItIsAPureExpression(): void
    {
        $expr = '{{[1,2,3]}}';
        $resolved = $this->expressionResolver->resolveExpression($expr, $this->variables);

        $this->assertEquals([1,2,3], $resolved);
    }

    public function testResolveExpressionMustReturnTheSameStringWhenThereAreNotPlaceHolders() : void
    {
        $literal = 'hi victor';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals($literal, $resolved);
    }

    public function testResolveExpressionMustReturnAStringWithPlaceHolderResolved() : void
    {
        $this->variables->add('name', 'Victor');
        $literal = 'hi {{name}}, the sum is {{ 1 + 1 }}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals('hi Victor, the sum is 2', $resolved);
    }

    public function testResolveExpressionMustReturnNumberOneWhenAPlaceHolderIsResolvedToTrue() : void
    {
        $literal = '{{ true }}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals('1', $resolved);
    }

    public function testResolveExpressionMustReturnEmptyStringWhenAPlaceHolderIsResolvedToFalse() : void
    {
        $literal = '{{ false }}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals('', $resolved);
    }

    public function testResolveExpressionMustReturnAStringWithPlaceHolderNotResoledWhenItConsistsInJustWitheSpaces() : void
    {
        $literal = 'hi {{ }}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals('hi {{ }}', $resolved);
    }

    public function testResolveExpressionMustReturnAStringWithPlaceHolderNotResolvedWhenItIsEmpty() : void
    {
        $literal = 'hi {{}}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals('hi {{}}', $resolved);
    }

    /**
     * @expectedException RecipeRunner\RecipeRunner\Expression\Exception\ErrorResolvingExpressionException
     * @expectedExceptionMessage List are not valid values for string interpolation. Expression: "my_items". Literal: "numbers {{my_items}}".
     */
    public function testResolveExpressionMustFailWhenAPlaceHolderIsResolvedToADictionary() : void
    {
        $this->variables->add('my_items', [1]);
        $literal = 'numbers {{my_items}}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);
    }

    /**
     * @expectedException RecipeRunner\RecipeRunner\Expression\Exception\ErrorResolvingExpressionException
     * @expectedExceptionMessage List are not valid values for string interpolation. Expression: "1..4". Literal: "numbers {{1..4}}".
     */
    public function testResolveExpressionMustFailWhenAPlaceHolderIsResolvedToAnArray() : void
    {
        $literal = 'numbers {{1..4}}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);
    }

    /**
     * @expectedException RecipeRunner\RecipeRunner\Expression\Exception\ErrorResolvingExpressionException
     * @expectedExceptionMessage Error resolving expression: "1..".
     */
    public function testResolveExpressionMustFailWhenBadExpression() : void
    {
        $literal = 'numbers {{1..}}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);
    }
}
