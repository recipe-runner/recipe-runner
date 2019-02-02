<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Module\Exception;

use Exception;
use RuntimeException;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;

class MethodNotFoundException extends RuntimeException
{
    /** @var Method */
    private $method;
    private $actionName;

    /**
     * Constructor.
     *
     * @param string $message The error message.
     * @param Method $method The method.
     * @param Exception $previous The previous exception.
     */
    public function __construct(string $message, Method $method, Exception $previous = null)
    {
        $this->method = $method;
        parent::__construct($message, 0, $previous);
    }

    /**
     * Returns the method.
     *
     * @return Method
     */
    public function getMethod() : Method
    {
        return $this->method;
    }
}
