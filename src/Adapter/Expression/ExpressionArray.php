<?php

namespace RecipeRunner\RecipeRunner\Adapter\Expression;

use ArrayAccess;
use Countable;
use Yosymfony\Collection\MixedCollection;

/**
 * ExpressionArray is a wrapper for each array value in Symfony Expression language.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class ExpressionArray implements ArrayAccess, Countable
{
    /** @var MixedCollection */
    private $values;

    public function __construct(array $values)
    {
        $this->values = new MixedCollection($values);
    }

    /**
     * Returns a value using a dot path. E.g: "registered.get('myVariable.success', false)"
     *
     * @param mixed $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        $value = $this->values->getDot($key, $defaultValue);

        return $this->convertArrayIntoExpressionArrayIfProceed($value);
    }

    /**
     * Returns if the given key exists.
     *
     * @return bool
     */
    public function has($key): bool
    {
        return $this->values->has($key);
    }

    /**
     * Count the number of items in the array.
     *
     * @return int
     */
    public function count() : int
    {
        return $this->values->count();
    }

    /**
     * Returns if exist an item at an offset.
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     *
     * @param  mixed  $key
     *
     * @return bool
     */
    public function offsetExists($key) : bool
    {
        return $this->values->offsetExists($key);
    }

    /**
     * Returns the item at a given offset.
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     *
     * @param  mixed  $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        $value = $this->values->offsetGet($key);

        return $this->convertArrayIntoExpressionArrayIfProceed($value);
    }

    /**
     * Sets the item at a given offset.
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value) : void
    {
        $this->values->offsetSet($key, $value);
    }

    /**
     * Unsets the item at a given offset.
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     *
     * @param  string  $key
     *
     * @return void
     */
    public function offsetUnset($key) : void
    {
        $this->values->offsetUnset($key);
    }

    public function convertArrayIntoExpressionArrayIfProceed($value)
    {
        if (!\is_array($value)) {
            return $value;
        }

        return new ExpressionArray($value);
    }
}
