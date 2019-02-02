<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\IO;

/**
 * Trait with a implementation for IOAwareInterface.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
trait IOTrait
{
    /** @var IOInterface */
    private $io;

    /**
     * Set the IO.
     *
     * @param IOInterface $io
     *
     * @return void
     */
    public function setIO(IOInterface $io): void
    {
        $this->io = $io;
    }

    /**
     * Returns the IO instance.
     *
     * @return IOInterface
     */
    protected function getIO(): IOInterface
    {
        if ($this->io !== null) {
            return $this->io;
        }
        
        $this->io = new NullIO();

        return $this->io;
    }
}
