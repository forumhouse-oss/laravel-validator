<?php

namespace FHTeam\LaravelValidator\Utility;

use ArrayAccess;
use ArrayIterator;
use Exception;
use Illuminate\Support\Str;
use IteratorAggregate;

/**
 * Class, incapsulating access to array-like data.
 * It provides convenient access to the underlying array.
 *
 * @package FHTeam\LaravelValidator\Utility
 */
class ArrayDataStorage implements ArrayAccess, IteratorAggregate
{
    const KEY_CASE_NO_CHANGE = 0;

    const KEY_CASE_SNAKE = 1;

    const KEY_CASE_STUDLY = 2;

    const KEY_CASE_CAMEL = 3;

    /**
     * @var array
     */
    protected $data = null;

    /**
     * @var callable
     */
    protected $keyCaseNormalizer;

    /**
     * @param int|callable $keyCase How to normalize keys when executing setXXXX methods. Either callable returning a
     *                              new key or one of the KEY_CASE_XXXXX constants
     *
     * @throws Exception
     */
    public function __construct($keyCase = self::KEY_CASE_NO_CHANGE)
    {
        $this->setKeyNormalizer($keyCase);
    }

    /**
     * Returns a single item
     *
     * @param $key
     *
     * @throws Exception
     * @return mixed
     */
    public function getItem($key)
    {
        $this->assertArrayDataExists();
        $this->assertKeyExists($key);

        return $this->data[$key];
    }

    /**
     * Checks if we have an item with the specified name
     *
     * @param $key
     *
     * @throws Exception
     * @return mixed
     */
    public function hasItem($key)
    {
        $this->assertArrayDataExists();

        return array_key_exists($key, $this->data);
    }

    /**
     * Returns a single item, if it is set or a default value, if it is not
     *
     * @param string     $key
     * @param null|mixed $default
     *
     * @return mixed
     * @throws Exception
     */
    public function getItemOrDefault($key, $default = null)
    {
        $this->assertArrayDataExists();

        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * Returns all items
     *
     * @return array
     */
    public function getItems()
    {
        $this->assertArrayDataExists();

        return $this->data;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     */
    public function setItem($key, $value)
    {
        $key = $this->normalizeKey($key);
        $this->data[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return void
     */
    public function unsetItem($key)
    {
        unset($this->data[$key]);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function setItems(array $data)
    {
        $this->data = [];
        foreach ($data as $key => $value) {
            $this->setItem($key, $value);
        }
    }\FHTeam\LaravelValidator\Utility\ArrayDataStorage::setItems

    /**
     * Returns only specified items
     *
     * @param array $keys            Keys which to return from array
     * @param bool  $respectKeyOrder If you want the resulting array to be ordered
     *                               precisely as $keys. Especially useful in list() construct
     * @param bool  $requireAllKeys  Require all keys from $keys to be present in the result. Throw exception otherwise
     *
     * @return array
     * @throws Exception
     */
    public function getOnly(array $keys, $respectKeyOrder = false, $requireAllKeys = true)
    {
        $this->assertArrayDataExists();

        $result = $respectKeyOrder ? Arr::onlyRespectOrder($this->data, $keys) : Arr::only($this->data, $keys);
        if ($requireAllKeys) {
            $this->assertAllDataPresent($keys, $result);
        }

        return $result;
    }

    /**
     * Returns values of only specified items
     *
     * @param array $keys            Keys which values to return from array
     * @param bool  $respectKeyOrder If you want the resulting array to be ordered
     *                               precisely as $keys. Especially useful in list() construct
     * @param bool  $requireAllKeys  Require all keys from $keys to be present in the result. Throw exception otherwise
     *
     * @return array
     * @throws Exception
     */
    public function getOnlyValues(array $keys, $respectKeyOrder = false, $requireAllKeys = true)
    {
        return array_values($this->getOnly($keys, $respectKeyOrder, $requireAllKeys));
    }

    /**
     * Returns all items except for the specified
     *
     * @param array $keys
     *
     * @return mixed
     */
    public function getExcept(array $keys)
    {
        $this->assertArrayDataExists();
        $result = Arr::except($this->data, $keys);

        return $result;
    }

    /**
     * IteratorAggregate implementation
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasItem($offset);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getItem($offset);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws Exception
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setItem($offset, $value);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset
     *
     * @throws Exception
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->unsetItem($offset);
    }

    /**
     * For testing if validated properties are set
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        $this->assertArrayDataExists();

        return isset($this->data[$name]);
    }

    /**
     * For unsetting properties
     *
     * @param string $name
     *
     * @return void
     */
    public function __unset($name)
    {
        $this->unsetItem($name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __get($name)
    {
        return $this->getItem($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->setItem($name, $value);
    }

    /**
     * Tests if all the given keys are present in the result
     *
     * @param array $keys
     * @param       $result
     *
     * @throws Exception
     */
    protected function assertAllDataPresent(array $keys, array $result)
    {
        if (count($result) !== count($keys)) {
            throw new Exception(
                "Cannot find the following data required: ".
                json_encode(array_diff_key($keys, $this->data))
            );
        }
    }

    /**
     * Asserts data exists at all
     *
     * @throws Exception
     */
    protected function assertArrayDataExists()
    {
        if (null === $this->data) {
            throw new Exception("Attempt to read non-existent data");
        }
    }

    /**
     * Asserts data exists at all
     *
     * @param string $key
     *
     * @throws Exception
     */
    protected function assertKeyExists($key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new Exception("Key '$key' does not exist. I have only ".json_encode($this->data));
        }
    }

    /**
     * Normalizes key name to access the data
     *
     * @param $key
     *
     * @return string
     */
    protected function normalizeKey($key)
    {
        $keyNormalizer = $this->keyCaseNormalizer;

        return $keyNormalizer($key);
    }

    /**
     * Key normalizer option for normalizeKey()
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function keyCaseNoChange($key)
    {
        return $key;
    }

    /**
     * Key normalizer option for normalizeKey()
     *
     * @param string $key
     *
     * @return string
     */
    protected function keyCaseSnake($key)
    {
        return Str::snake($key);
    }

    /**
     * Key normalizer option for normalizeKey()
     *
     * @param string $key
     *
     * @return string
     */
    protected function keyCaseStudly($key)
    {
        return Str::studly($key);
    }

    /**
     * Key normalizer option for normalizeKey()
     *
     * @param string $key
     *
     * @return string
     */
    protected function keyCaseCamel($key)
    {
        return Str::camel($key);
    }

    /**
     * @param $keyCase
     *
     * @throws Exception
     */
    public function setKeyNormalizer($keyCase)
    {
        if (null !== $this->data) {
            throw new Exception("Cannot change key normalizer if data was already filled in");
        }

        if (is_callable($keyCase)) {
            $this->keyCaseNormalizer = $keyCase;
        } else {
            switch ($keyCase) {
                case self::KEY_CASE_NO_CHANGE:
                    $this->keyCaseNormalizer = [$this, 'keyCaseNoChange'];
                    break;
                case self::KEY_CASE_SNAKE:
                    $this->keyCaseNormalizer = [$this, 'keyCaseSnake'];
                    break;
                case self::KEY_CASE_STUDLY:
                    $this->keyCaseNormalizer = [$this, 'keyCaseStudly'];
                    break;
                case self::KEY_CASE_CAMEL:
                    $this->keyCaseNormalizer = [$this, 'keyCaseCamel'];
                    break;
                default:
                    throw new Exception("Unsupported key case option '$keyCase'");
            }
        }
    }
}
