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
 * Interface for creating setions.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
interface IOSectionAwareInterface
{
    /**
    * Creates a new region called section.
    * Sections let you manipulate the output in advanced ways.
    */
    public function createSection(): IOSectionInterface;
}
