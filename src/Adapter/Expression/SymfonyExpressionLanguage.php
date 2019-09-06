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

/**
 * Expression resolver for Symfony expression language.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
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
     *
     * Examples of expressions:
     *  - "Hi, the sum is {{1+1}}" will be resolved as a STRING: "Hi, the sum is 2"
     *  - "{{[1,2]}}" is a "pure" expression and will be resolved as and array.
     */
    public function resolveExpression(string $literal, CollectionInterface $variables)
    {
        if ($this->isAPureExpression($literal)) {
            $expression = $this->getExpressionFromPureExpression($literal);

            return $this->internalResolveExpression($expression, $variables);
        }

        return \preg_replace_callback(self::$interpolationPatter, function ($match) use ($literal, $variables) {
            if (!isset($match[1])) {
                return self::INTERPOLATION_OPEN.self::INTERPOLATION_CLOSE;
            }

            $expression = $match[1];

            if (\trim($expression) === '') {
                return self::INTERPOLATION_OPEN.$expression.self::INTERPOLATION_CLOSE;
            }

            $resolved = $this->internalResolveExpression($expression, $variables);

            if (\is_array($resolved)) {
                $message = "List are not valid values for string interpolation. Expression: \"{$expression}\". Literal: \"{$literal}\".";
                throw new ErrorResolvingExpressionException($message, $expression);
            }

            return \sprintf('%s', $resolved);
        }, $literal);
    }
    

    /**
     * {@inheritdoc}
     */
    public function resolveBooleanExpression(string $expression, CollectionInterface $variables) : bool
    {
        $result = $this->internalResolveExpression($expression, $variables);

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
        $values = $this->internalResolveExpression($expression, $variables);

        if (is_array($values)) {
            return new MixedCollection($values);
        }

        $message = "Expression \"{$expression}\" is not a list expression.";
        throw new ErrorResolvingExpressionException($message, $expression);
    }

    private function internalResolveExpression(string $expression, CollectionInterface $variables)
    {
        $adaptedVariables = $this->convertVariableCollectionIntoArrayExpression($variables);

        try {
            return $this->expressionLanguage->evaluate($expression, $adaptedVariables);
        } catch (Exception $ex) {
            $message = "Error resolving expression: \"{$expression}\". Details: {$ex->getMessage()}";
            throw new ErrorResolvingExpressionException($message, $expression);
        }
    }

    private function isAPureExpression(string $literal): bool
    {
        $beginning = \substr($literal, 0, strlen(self::INTERPOLATION_OPEN));
        $end = \substr($literal, -\strlen(self::INTERPOLATION_CLOSE));

        if ($beginning === self::INTERPOLATION_OPEN && $end === self::INTERPOLATION_CLOSE) {
            return true;
        }

        return false;
    }

    private function getExpressionFromPureExpression(string $literal): string
    {
        $expression = \substr($literal, strlen(self::INTERPOLATION_OPEN));
        $expression = \substr($expression, 0, -strlen(self::INTERPOLATION_CLOSE));
        $expression = \trim($expression);

        return $expression;
    }

    private function convertVariableCollectionIntoArrayExpression(CollectionInterface $variables): array
    {
        $variablesAsArray = [];

        foreach ($variables->toArray() as $key => $value) {
            $variablesAsArray[$key] = !\is_array($value) ? $value : new Dictionary($value);
        }

        return $variablesAsArray;
    }
}
