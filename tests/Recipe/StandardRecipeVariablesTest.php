<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Recipe;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Recipe\StandardRecipeVariables;

class StandardRecipeVariablesTest extends TestCase
{
    public function testGetCollectionOfVariables(): void
    {
        $collection = StandardRecipeVariables::getCollectionOfVariables();

        $this->assertCount(4, $collection);
        $this->assertEquals(PHP_OS_FAMILY, $collection['os_family']);
        $this->assertEquals(DIRECTORY_SEPARATOR, $collection['dir_separator']);
        $this->assertEquals(PATH_SEPARATOR, $collection['path_separator']);
        $this->assertEquals(sys_get_temp_dir(), $collection['temporal_dir']);
    }
}
