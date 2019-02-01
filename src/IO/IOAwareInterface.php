<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\IO;

/**
 * The interface should be implemented for classes that depend on a IO.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
interface IOAwareInterface
{
    /**
     * Sets the IO.
     *
     * @param IOInterface $io
     *
     * @return void
     */
    public function setIO(IOInterface $io): void;
}
