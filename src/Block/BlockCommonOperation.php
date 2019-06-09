<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Block;

use RecipeRunner\RecipeRunner\Expression\ExpressionResolverInterface;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class BlockCommonOperation
{
    /** @var ExpressionResolverInterface */
    protected $expressionResolver;

    /**
     * Constructor.
     *
     * @param ExpressionResolverInterface $expressionResolver
     */
    public function __construct(ExpressionResolverInterface $expressionResolver)
    {
        $this->expressionResolver = $expressionResolver;
    }

    /**
     * Returns a collection with the variables of a loop.
     *
     * @param mixed $index
     * @param mixed $value
     * @param string $rootItemName
     *
     * @return CollectionInterface
     */
    public function generateLoopVariables($index, $value, string $rootItemName = 'loop') : CollectionInterface
    {
        return new MixedCollection([
            $rootItemName => [
                'index' => $index,
                'value' => $value,
            ]
        ]);
    }

    /**
     * Evaluates a "When" expression.
     *
     * @param string $expression
     * @param CollectionInterface $recipeVariables
     *
     * @return bool
     */
    public function evaluateWhenCondition(string $expression, CollectionInterface $recipeVariables) : bool
    {
        if ($expression == '') {
            return true;
        }

        return $this->expressionResolver->resolveBooleanExpression($expression, $recipeVariables);
    }

    /**
     * Evaluates a loop expression if it is a string.
     *
     * @param string $expression
     * @param CollectionInterface $recipeVariables
     *
     * @return bool
     */
    public function evaluateLoopExpressionIfItIsString($loopExpression, CollectionInterface $recipeVariables) : CollectionInterface
    {
        if (\is_string($loopExpression)) {
            return $this->expressionResolver->resolveCollectionExpression($loopExpression, $recipeVariables);
        }

        return $loopExpression;
    }
}
