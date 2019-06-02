<?php

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
