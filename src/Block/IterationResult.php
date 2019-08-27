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
    public const STATUS_SKIPPED = 0;
    public const STATUS_SUCCESSFUL = 1;
    public const STATUS_FAILED = 2;

    /** @var int */
    private $status;

    public function __construct(int $status)
    {
        if ($status < 0 || $status > 2) {
            throw new \InvalidArgumentException('Invalid iteration status.');
        }

        $this->status = $status;
    }

    /**
     * Indicates if the block iteration was skipped.
     *
     * @return bool
     */
    public function isSkipped(): bool
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    /**
     * Indicates if the block iteration was executed successfully.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    /**
     * Indicates the block iteration execution failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
