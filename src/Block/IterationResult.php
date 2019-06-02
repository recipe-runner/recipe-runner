<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Block;

/**
 * Result of a block iteration.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class IterationResult
{
    private $isExecuted;
    private $isSuccessful;

    public function __construct(bool $isExecuted, bool $isSuccessful)
    {
        $this->isExecuted = $isExecuted;
        $this->isSuccessful = $isSuccessful;
    }

    /**
     * Indicates if the block was executed in the iteration.
     *
     * @return bool
     */
    public function isExecuted(): bool
    {
        return $this->isExecuted;
    }

    /**
     * Indicates if the block was executed in the iteration successfully.
     */
    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }
}
