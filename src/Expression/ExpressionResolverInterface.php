<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Expression;

use Yosymfony\Collection\CollectionInterface;

interface ExpressionResolverInterface
{
    /**
     * Resolves a expression.
     *
     * @param string $expression
     * @param CollectionInterface $variables
     *
     * @return mixed
     */
    public function resolveExpression(string $expression, CollectionInterface $variables);

    /**
     * Resolves a boolean expression.
     *
     * @param string $expression
     * @param CollectionInterface $variables
     *
     * @return bool
     *
     * @throws RecipeRunner\Expression\Exception\ErrorResolvingExpressionException If expression's result is not boolean.
     */
    public function resolveBooleanExpression(string $expression, CollectionInterface $variables) : bool;

    /**
     * Resolves a collection expression.
     *
     * @param string $expression
     * @param CollectionInterface $variables
     *
     * @return bool
     *
     * @throws RecipeRunner\Expression\Exception\ErrorResolvingExpressionException If expression's result is not a collection.
     */
    public function resolveCollectionExpression(string $expression, CollectionInterface $variables) : CollectionInterface;

    /**
     * Resolve string interpolations.
     *
     * @param string $literal String literal with placeholders. e.g: "Hi {{ name }}"
     * @param CollectionInterface $variables
     *
     * @return string
     */
    public function resolveStringInterpolation(string $literal, CollectionInterface $variables) : string;
}
