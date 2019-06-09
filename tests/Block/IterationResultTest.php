<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Block;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Block\IterationResult;

class IterationResultTest extends TestCase
{
    public function testInitialization(): void
    {
        $result = new IterationResult(true, true);

        $this->assertTrue($result->isExecuted());
        $this->assertTrue($result->isSuccessful());
    }
}
