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
 * Non-interactive implementation of IO Section interface that never writes the output.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class NullIOSection extends NullIO
{
    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function override($message): void
    {
    }
}
