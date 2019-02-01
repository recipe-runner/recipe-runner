<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner;

use RecipeRunner\Expression\ExpressionResolverInterface;
use RecipeRunner\IO\IOAwareInterface;
use RecipeRunner\IO\IOTrait;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class ParserBase implements IOAwareInterface
{
    use IOTrait;

    /** @var ExpressionResolverInterface */
    protected $expressionResolver;

    /**
     * Constructor.
     *
     * @param ExpressionResolverInterface $expressionResolver
     */
    protected function __construct(ExpressionResolverInterface $expressionResolver)
    {
        $this->expressionResolver = $expressionResolver;
    }

    protected function generateLoopVariables($index, $value, string $rootItemName = 'loop') : CollectionInterface
    {
        return new MixedCollection([
            $rootItemName => [
                'index' => $index,
                'value' => $value,
            ]
        ]);
    }

    protected function evaluateWhenCondition(string $expression, CollectionInterface $recipeVariables) : bool
    {
        if ($expression == '') {
            return true;
        }

        return $this->expressionResolver->resolveBooleanExpression($expression, $recipeVariables);
    }

    protected function evaluateLoopExpressionIfItIsString($loopExpression, CollectionInterface $recipeVariables) : CollectionInterface
    {
        if (\is_string($loopExpression)) {
            return $this->expressionResolver->resolveCollectionExpression($loopExpression, $recipeVariables);
        }

        return $loopExpression;
    }
}
