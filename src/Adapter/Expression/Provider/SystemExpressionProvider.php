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

use InvalidArgumentException;

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
            $this->createExpressionFunction('version_compare', 'versionCompare'),
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

    /**
     * Returns Compare two version strings following the pattern "mayor.minor.patch".
     *
     * @param string $version1
     * @param string $operator Operator. Valid values: <, <=, >, >=, =, !=.
     * @param string $version2
     *
     * @return bool True if the relationship is the one specified by the operator, false otherwise.
     */
    public static function versionCompare(string $version1, string $operator, string $version2): bool
    {
        $version1 = \strtolower($version1);
        $version2 = \strtolower($version2);
        try {
            return \version_compare($version1, $version2, $operator);
        } catch (\Throwable $th) {
            throw new InvalidArgumentException("Invalid operator \"$operator\". Expected: <, <=, >, >=, =, !=.");
        }
    }
}
