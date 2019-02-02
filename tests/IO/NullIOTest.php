<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\IO;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\IO\NullIO;

class NullIOTest extends TestCase
{
    /** @var NullIO */
    private $io;

    public function setUp(): void
    {
        $this->io = new NullIO();
    }

    public function testIsInteractiveMustReturnFalse(): void
    {
        $this->assertFalse($this->io->isInteractive());
    }

    public function testAskMustReturnTheDefaultValue(): void
    {
        $this->assertEquals('Víctor', $this->io->ask('What is your name?', 'Víctor'));
    }

    public function testAskConfirmationMustReturnTheDefaultValue(): void
    {
        $this->assertEquals(true, $this->io->askConfirmation('Would you like to start?', true));
    }

    public function testAskWithHiddenResponseMustReturnEmptyString(): void
    {
        $this->assertEquals('', $this->io->askWithHiddenResponse('Database password?'));
    }

    public function testAskChoiceMustReturnTheDefaultValue(): void
    {
        $this->assertEquals(0, $this->io->askChoice('Pick one', ['a', 'b'], 0));
    }

    public function testAskMultiselectChoiceMustReturnTheDefaultValue(): void
    {
        $this->assertEquals([0], $this->io->askMultiselectChoice('Pick one', ['a', 'b'], 0));
    }
}
