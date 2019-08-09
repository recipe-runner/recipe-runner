<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Adapter\Expression\Provider;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Adapter\Expression\Provider\SystemExpressionProvider;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use Yosymfony\Collection\MixedCollection;

class SystemExpressionProviderTest extends TestCase
{
    public function testGetFunctionsMustReturnAllTheFunctionRegistered(): void
    {
        $provider = new SystemExpressionProvider();

        $this->assertCount(2, $provider->getFunctions());
    }

    public function testEnvMustReturnTheValueOfAnEnvironmentVariable(): void
    {
        \putenv('name=Víctor');
        
        $result = SystemExpressionProvider::env('name');

        $this->assertEquals('Víctor', $result);
    }

    public function testVersionCompare(): void
    {
        $this->assertTrue(SystemExpressionProvider::versionCompare('1.0.0', '>', '0.1.0'));
        $this->assertTrue(SystemExpressionProvider::versionCompare('1.0.0', '>=', '1.0.0'));
        $this->assertFalse(SystemExpressionProvider::versionCompare('0.1.0', '>', '1.0.0'));

        $this->assertTrue(SystemExpressionProvider::versionCompare('0.1.0', '<', '1.0.0'));
        $this->assertFalse(SystemExpressionProvider::versionCompare('1.0.0', '<', '1.0.0'));
        $this->assertTrue(SystemExpressionProvider::versionCompare('1.0.0', '<=', '1.0.0'));

        $this->assertTrue(SystemExpressionProvider::versionCompare('1.0.0', '>', '1.0.0rc1'));
        $this->assertTrue(SystemExpressionProvider::versionCompare('1.0.0rc1', '>', '1.0.0b1'));
    }

    /**
    * @expectedException InvalidArgumentException
    * @expectedExceptionMessage Invalid operator "-". Expected: <, <=, >, >=, =, !=.
    */
    public function testVersionCompareMustFailWhenInvalidOperator(): void
    {
        SystemExpressionProvider::versionCompare('1.0.0', '-', '0.1.0');
    }
}
