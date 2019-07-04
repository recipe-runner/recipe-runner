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

/**
 * Provider for system functions.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class SystemExpressionProvider extends ExpressionProviderBase
{
    public function getFunctions()
    {
        return [
            $this->createExpressionFunction('env', 'env'),
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
}
