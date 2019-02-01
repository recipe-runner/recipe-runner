<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Expression\Exception;

use Exception;
use RuntimeException;

class ErrorResolvingExpressionException extends RuntimeException
{
    private $expression;

    /**
     * Constructor.
     *
     * @param string $message The error message.
     * @param Method $method The method.
     * @param Exception $previous The previous exception.
     */
    public function __construct(string $message, string $expression, Exception $previous = null)
    {
        $this->expression = $expression;
        parent::__construct($message, 0, $previous);
    }

    public function getExpression() : string
    {
        return $this->expression;
    }
}
