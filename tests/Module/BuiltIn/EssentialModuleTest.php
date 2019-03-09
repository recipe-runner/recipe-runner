<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Module\BuiltIn;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Module\BuiltIn\EssentialModule;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use Yosymfony\Collection\MixedCollection;

class EssentialModuleTest extends TestCase
{
    /** @var EssentialModule*/
    private $module;

    /** @var Method */
    private $method;

    public function setUp(): void
    {
        $this->module = new EssentialModule();
        $this->method = new Method('register_variables');
    }

    public function testMethodRegisterVariableMustRegisterVariables(): void
    {
        $this->method->addParameter('user', 'Víctor');

        $response = $this->module->runMethod($this->method, new MixedCollection());
        $arrayResponse = \json_decode($response->getJsonResult(), true);
        
        $this->assertEquals([
            'user' => 'Víctor',
        ], $arrayResponse);
    }

    /**
    * @expectedException InvalidArgumentException
    * @expectedExceptionMessage No variables have been declared at method "register_variables".
    */
    public function testMethodRegisterVariableMustFailWhenThereIsNoVariables(): void
    {
        $this->module->runMethod($this->method, new MixedCollection());
    }
}
