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

class SystemExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            $this->createExpressionFunction('env', SystemExpressionLanguageProvider::class.'::env'),
        ];
    }

    /**
     * Returns the value of an environment variable.
     *
     * @param string $name The variable name.
     *
     * @return string The value or empty string if the environment variable does not exist.
     */
    public static function env(string $name): string
    {
        $value = \getenv($name);

        if ($value === false) {
            return '';
        }

        return $value;
    }

    private function createExpressionFunction(string $name, string $staticFunctionName): ExpressionFunction
    {
        $compiler = function () use ($staticFunctionName) {
            return sprintf('\%s(%s)', $staticFunctionName, implode(', ', \func_get_args()));
        };
        $evaluator = function () use ($staticFunctionName) {
            return $staticFunctionName(...\array_slice(\func_get_args(), 1));
        };

        return new ExpressionFunction($name, $compiler, $evaluator);
    }
}
