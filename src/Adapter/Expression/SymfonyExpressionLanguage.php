<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Adapter\Expression;

use Exception;
use RecipeRunner\RecipeRunner\Adapter\Expression\Provider\SystemExpressionProvider;
use RecipeRunner\RecipeRunner\Expression\Exception\ErrorResolvingExpressionException;
use RecipeRunner\RecipeRunner\Expression\ExpressionResolverInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class SymfonyExpressionLanguage implements ExpressionResolverInterface
{
    private const INTERPOLATION_OPEN = '{{';
    private const INTERPOLATION_CLOSE = '}}';
    private static $interpolationPatter = '/{{([^{}]+)}}/';

    /** @var ExpressionLanguage */
    private $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
        $this->expressionLanguage->registerProvider(new SystemExpressionProvider());
    }

    /**
     * {@inheritdoc}
     */
    public function resolveExpression(string $expression, CollectionInterface $variables)
    {
        try {
            return $this->expressionLanguage->evaluate($expression, $variables->toArray());
        } catch (Exception $ex) {
            $message = "Error resolving expression: \"{$expression}\". Details: {$ex->getMessage()}";
            throw new ErrorResolvingExpressionException($message, $expression);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resolveBooleanExpression(string $expression, CollectionInterface $variables) : bool
    {
        $result = $this->resolveExpression($expression, $variables);

        if ($result === true || $result === false) {
            return $result;
        }

        $message = "Expression \"{$expression}\" is not a boolean expression.";
        throw new ErrorResolvingExpressionException($message, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCollectionExpression(string $expression, CollectionInterface $variables) : CollectionInterface
    {
        $values = $this->resolveExpression($expression, $variables);

        if (is_array($values)) {
            return new MixedCollection($values);
        }

        $message = "Expression \"{$expression}\" is not a list expression.";
        throw new ErrorResolvingExpressionException($message, $expression);
    }

    /**
     * {@inheritdoc}
     *
     * Example: "The sum is 2: {{ 1 + 2 }}" -> "The sum is 2: 2".
     */
    public function resolveStringInterpolation(string $literal, CollectionInterface $variables) : string
    {
        return \preg_replace_callback(self::$interpolationPatter, function ($match) use ($literal, $variables) {
            if (!isset($match[1])) {
                return self::INTERPOLATION_OPEN.self::INTERPOLATION_CLOSE;
            }

            $expression = $match[1];

            if (\trim($expression) === '') {
                return self::INTERPOLATION_OPEN.$expression.self::INTERPOLATION_CLOSE;
            }

            $resolved = $this->resolveExpression($expression, $variables);

            if (\is_array($resolved)) {
                $message = "List are not valid values for string interpolation. Expression: \"{$expression}\". Literal: \"{$literal}\".";
                throw new ErrorResolvingExpressionException($message, $expression);
            }

            return \sprintf('%s', $resolved);
        }, $literal);
    }
}
