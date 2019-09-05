<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use Yosymfony\Collection\MixedCollection;

class SymfonyExpressionLanguageAndArraysTest extends TestCase
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

    public function testAccessSubArray(): void
    {
        $expected = 'test';
        $this->variables->add('values', [
            'level1' => [
                'level2' => $expected,
            ],
        ]);
        $literal = '{{values["level1"]["level2"]}}';

        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals($expected, $resolved);
    }

    public function testGetMethodWithDotPathMustReturnTheValueAtTheEndOfThePath() : void
    {
        $expected = 'Alex';
        $this->variables->add('values', [
            'names' => [
                'first' => $expected,
                'second' => 'Víctor',
            ],
        ]);
        $literal = '{{values.get("names.first")}}';

        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals($expected, $resolved);
    }

    public function testGetMethodMustReturnDefaultValueWhenThePathDoesNotExist() : void
    {
        $this->variables->add('values', [
            'names' => [
                'second' => 'Víctor',
            ],
        ]);
        $literal = '{{values.get("names.first", "Alex")}}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertEquals('Alex', $resolved);
    }

    public function testHasMethodMustReturnTrueWhenTheKeyExists() : void
    {
        $this->variables->add('values', ['value1' => 1]);
        $literal = '{{values.has("value1")}}';
        $resolved = $this->expressionResolver->resolveExpression($literal, $this->variables);

        $this->assertTrue($resolved);
    }
}
