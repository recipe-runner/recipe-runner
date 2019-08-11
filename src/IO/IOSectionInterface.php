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
 * Sections let you manipulate the output in advanced ways.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
interface IOSectionInterface extends IOInterface
{
    /**
     * Clears previous output for this section.
     */
    public function clear(): void;

    /**
     * Overwrites the previous output with a new message.
     *
     * @param array|string $message
     */
    public function override($message): void;
}
