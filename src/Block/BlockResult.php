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

use InvalidArgumentException;
use RecipeRunner\RecipeRunner\Block\IterationResult;
use Yosymfony\Collection\CollectionInterface;

/**
 * Result of a block (step or action).
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class BlockResult
{
    /** @var int */
    private $parentBlockIterationNumber;

    /** @var string */
    private $blockId;

    /** @var string */
    private $parentBlockId;
    

    /** @var CollectionInterface */
    private $iterationResults;

    /**
     * Constructor.
     *
     * @param CollectionInterface $iterationResults Collection of IterationResult
     */
    public function __construct(string $blockId, CollectionInterface $iterationResults)
    {
        $this->validateBlockId($blockId);
        $this->parentBlockIterationNumber = 0;
        $this->blockId = $blockId;
        $this->iterationResults = $iterationResults;
    }

    /**
     * Returns the id of the block that generate this result.
     *
     * @return string
     */
    public function getBlockId(): string
    {
        return $this->blockId;
    }

    /**
     * A block execution is failed when there is at least one
     * iteration failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->iterationResults->any(function (IterationResult $iterationResult) {
            return $iterationResult->isFailed();
        });
    }

    /**
     * A block execution is skipped when all iteration are skipped.
     *
     * @return bool
     */
    public function isSkipped(): bool
    {
        return $this->iterationResults->every(function (IterationResult $iterationResult) {
            return $iterationResult->isSkipped();
        });
    }

    /**
     * Returns the number of iterations.
     *
     * @return int
     */
    public function getNumberOfIterations(): int
    {
        return count($this->iterationResults);
    }

    /**
     * Returns the iteration result at the index passed.
     *
     * @param int $index Positive value starting at zero.
     *
     * @return IterationResult
     */
    public function getIterationResultAt(int $index): IterationResult
    {
        if ($index < 0) {
            throw new InvalidArgumentException('Iteration index must be a positive value starting at zero.');
        }

        if ($index >= $this->getNumberOfIterations()) {
            throw new InvalidArgumentException("The iteration index \"{$index}\" is out of range.");
        }

        $currentIndex = 0;

        foreach ($this->iterationResults as $iterationResult) {
            if ($currentIndex == $index) {
                return $iterationResult;
            }

            $currentIndex++;
        }
    }

    /**
     * Sets the parent block id and the iteration number.
     *
     * @param string $id Parent block id.
     * @param int $iterationNumber Iteration number of the parent.
     *
     * @return void
     */
    public function setParentBlockData(string $id, int $iterationNumber): void
    {
        $this->validateBlockId($id);

        if ($iterationNumber < 0) {
            throw new InvalidArgumentException('Iteration number must be a positive value based on zero.');
        }

        $this->parentBlockId = $id;
        $this->parentBlockIterationNumber = $iterationNumber;
    }

    /**
     * Returns the parent block id.
     *
     * @return string
     */
    public function getParentBlockId(): string
    {
        return $this->parentBlockId;
    }

    public function getParentIterationNumber(): int
    {
        return $this->parentBlockIterationNumber;
    }

    private function validateBlockId(string $id): void
    {
        if (\strlen(\trim($id)) === 0) {
            throw new InvalidArgumentException('Invalid identifier. Only non-empty values are allowed.');
        }
    }
}
