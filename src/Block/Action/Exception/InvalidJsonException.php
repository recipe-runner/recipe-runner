<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Block\Action\Exception;

use Exception;
use RuntimeException;

/**
 * Exception that is thrown when the execution of a method returns a bad formed JSON string.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class InvalidJsonException extends RuntimeException
{
    private $json;

    /**
     * Constructor.
     *
     * @param string $message The error message.
     * @param Method $method The method.
     * @param Exception $previous The previous exception.
     */
    public function __construct(string $message, string $json, Exception $previous = null)
    {
        $this->json = $json;
        parent::__construct($message, 0, $previous);
    }
    
    /**
     * Returns the JSON string that thrown the exception.
     */
    public function getJson() : string
    {
        return $this->json;
    }
}
