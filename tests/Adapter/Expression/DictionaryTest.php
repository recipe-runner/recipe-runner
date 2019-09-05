<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Adapter\Expression;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Adapter\Expression\Dictionary;

class DictionaryTest extends TestCase
{
    public function testAnySubArrayMustBeTurnedIntoAnExpressionArray(): void
    {
        $dictionary = $this->make([
            'values' => [1,2,3],
        ]);

        $this->assertInstanceOf(Dictionary::class, $dictionary['values']);
        $this->assertInstanceOf(Dictionary::class, $dictionary->get('values'));
    }

    public function testGetMustReturnTheValueAtTheEndOfThePath(): void
    {
        $expected = 'Alex';
        $arr = $this->make([
            'names' => [
                'first' => $expected,
                'second' => 'Víctor'
            ],
        ]);

        $this->assertEquals($expected, $arr->get('names.first'));
    }

    public function testGetMustReturnTheDefaultValueTheDotPathDoesNotExist(): void
    {
        $expected = 'Alex';
        $arr = $this->make([
            'names' => [
                'second' => 'Víctor'
            ],
        ]);

        $this->assertEquals($expected, $arr->get('names.first', $expected));
    }

    public function testHasMustReturnTrueWhenTheKeyExists(): void
    {
        $arr = $this->make([
            'name' => 'Alex'
        ]);

        $this->assertTrue($arr->has('name'));
    }

    public function testHasMustReturnFalseWhenTheKeyDoesNotExist(): void
    {
        $arr = $this->make([
            'name' => 'Alex'
        ]);

        $this->assertFalse($arr->has('name1'));
    }
    
    public function testCountMustReturnTheNumberOfItemsInTheCollection() : void
    {
        $arr = $this->make([
            'name' => 'Yo! Symfony',
            'port' => 443,
        ]);

        $this->assertCount(2, $arr);
    }

    public function testOffsetExistsMustReturnTrueWhenKeyExists()
    {
        $key = 'first';
        $arr = $this->make([$key => 1]);

        $this->assertTrue(isset($arr[$key]));
    }

    public function testOffsetExistsMustReturnFalseWhenKeyDoesNotExist()
    {
        $arr = $this->make(['first' => 1]);

        $this->assertFalse(isset($arr['second']));
    }

    public function testOffsetGetMustReturnTheValueAssociatedWithTheKey()
    {
        $key = 'first';
        $value = 1;
        $arr = $this->make([$key => $value]);

        $this->assertEquals($value, $arr[$key]);
    }

    public function testOffsetSetMustSetTheValueAssociatedWithTheKey()
    {
        $key = 'first';
        $value = 1;
        $arr = $this->make();

        $arr[$key] = $value;
        $arr[] = $value;

        $this->assertCount(2, $arr);
        $this->assertEquals($value, $arr[$key]);
        $this->assertEquals($value, $arr[0]);
    }

    public function testOffsetUnsetMustDestroyTheValueAssociatedWithTheKeyAndTheKey()
    {
        $key = 'first';
        $value = 1;
        $arr = $this->make([$key => $value]);

        unset($arr[$key]);

        $this->assertCount(0, $arr);
    }

    private function make(array $values = []) : Dictionary
    {
        return new Dictionary($values);
    }
}
