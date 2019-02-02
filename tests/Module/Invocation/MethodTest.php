<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Module\Invocation;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;

class MethodTest extends TestCase
{
    /** @var Method */
    private $method;

    public function setUp() : void
    {
        $this->method = new Method('foo');
    }

    public function testGetNameMustReturnTheNameOfTheMethod() : void
    {
        $this->assertEquals('foo', $this->method->getName());
    }

    public function testAddParameterMustAddANewParameter() : void
    {
        $this->method->addParameter('name', 'Victor');

        $this->assertEquals([
            'name' => 'Victor',
        ], $this->method->getParameters()->toArray());
    }

    public function testGetParametersMustReturnAllParametersAdded() : void
    {
        $this->method->addParameter('name', 'Victor');

        $this->assertEquals([
            'name' => 'Victor',
        ], $this->method->getParameters()->toArray());
    }

    public function testGetParameterNameOrPositionMustReturnTheParameterAssociatedWithTheName() : void
    {
        $this->method->addParameter('name', 'Victor');

        $value = $this->method->getParameterNameOrPosition('name', 0);

        $this->assertEquals('Victor', $value);
    }

    public function testGetParameterNameOrPositionMustReturnTheParameterInThePositionWhenParameterNameNotFound() : void
    {
        $this->method->addParameter('name', 'Victor');

        $value = $this->method->getParameterNameOrPosition('name2', 0);

        $this->assertEquals('Victor', $value);
    }

    public function testGetParameterNameOrPositionMustReturnTheDefaultValueWhenParameterNameOrPositionNotFound() : void
    {
        $default = $this->method->getParameterNameOrPosition('name', 0, 'Victor');
        $defaultNull = $this->method->getParameterNameOrPosition('name', 0);

        $this->assertEquals('Victor', $default);
        $this->assertNull($defaultNull);
    }
}
