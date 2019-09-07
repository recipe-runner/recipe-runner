<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Adapter\Expression\Provider;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Abstract class for expression provider.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
abstract class ExpressionProviderBase implements ExpressionFunctionProviderInterface
{
    /**
     * Create an expression function.
     *
     * @param string $name Name of the function.
     * @param string $staticFunctionName of the static function.
     *
     * @return ExpressionFunction
     */
    protected function createExpressionFunction(string $name, string $staticFunctionName): ExpressionFunction
    {
        $staticFunctionName = \get_class($this)."::{$staticFunctionName}";
        $compiler = function () use ($staticFunctionName) {
            return sprintf('\%s(%s)', $staticFunctionName, implode(', ', \func_get_args()));
        };
        $evaluator = function () use ($staticFunctionName) {
            return $staticFunctionName(...\array_slice(\func_get_args(), 1));
        };

        return new ExpressionFunction($name, $compiler, $evaluator);
    }
}
