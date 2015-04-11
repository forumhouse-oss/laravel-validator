<?php

namespace FHTeam\LaravelValidator\Tests\Utility;

use Exception;
use FHTeam\LaravelValidator\Tests\TestBase;
use FHTeam\LaravelValidator\Utility\ArrayDataStorage;

/**
 * Class ArrayDataStorageTest
 *
 * @package FHTeam\LaravelValidator\Test\Utility
 */
class ArrayDataStorageTest extends TestBase
{
    /**
     * @var ArrayDataStorage
     */
    protected $storage;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->storage = new ArrayDataStorage();
    }

    /**
     * @throws \Exception
     */
    public function testSimpleGetSet()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_NO_CHANGE);
        $this->storage->setItem('test_ItemS', 'testValue');
        $this->assertEquals('testValue', $this->storage->getItem('test_ItemS'));
        $this->assertEquals('testValue', $this->storage->offsetGet('test_ItemS'));
    }

    /**
     * @throws \Exception
     */
    public function testKeyNormalizersCamel()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_CAMEL);
        $this->storage->setItem('test_item_1', 'testValue1');
        $this->storage->offsetSet('TestItem2', 'testValue2');
        $this->assertEquals('testValue1', $this->storage->getItem('testItem1'));
        $this->assertEquals('testValue1', $this->storage->offsetGet('testItem1'));
        $this->assertEquals('testValue2', $this->storage->offsetGet('testItem2'));
    }

    /**
     * @throws \Exception
     */
    public function testKeyNormalizerSnake()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_SNAKE);
        $this->storage->setItem('testItem1', 'testValue1');
        $this->storage->offsetSet('TestItem2', 'testValue2');
        $this->assertEquals('testValue1', $this->storage->getItem('test_item1'));
        $this->assertEquals('testValue2', $this->storage->getItem('test_item2'));
    }

    /**
     * @throws \Exception
     */
    public function testKeyNormalizerStudly()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_STUDLY);
        $this->storage->setItem('testItem1', 'testValue1');
        $this->storage->offsetSet('test_item2', 'testValue2');
        $this->assertEquals('testValue1', $this->storage->getItem('TestItem1'));
        $this->assertEquals('testValue2', $this->storage->getItem('TestItem2'));
    }

    /**
     * @throws \Exception
     */
    public function testKeyNormalizerCustom()
    {
        $this->storage->setKeyNormalizer(
            function ($value) {
                return "-$value-";
            }
        );
        $this->storage->setItem('testItem1', 'testValue1');
        $this->assertEquals('testValue1', $this->storage->getItem('-testItem1-'));
    }

    /**
     * @throws \Exception
     */
    public function testSimpleGetSetArray()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_NO_CHANGE);
        $this->storage->setItems(['testItem1' => 'testValue1', 'testItem2' => 'testValue2']);
        $this->assertEquals('testValue1', $this->storage->getItem('testItem1'));
        $this->assertEquals('testValue2', $this->storage->getItem('testItem2'));
    }

    /**
     * @throws \Exception
     */
    public function testArrayDataNotExistsException()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_NO_CHANGE);
        $this->setExpectedException(Exception::class);
        $this->storage->getItem('testItem1');
    }

    /**
     * @throws \Exception
     */
    public function testKeyNotExists()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_NO_CHANGE);
        $this->setExpectedException(Exception::class);
        $this->storage->setItem('testItem1', 'testValue1');
        $this->storage->getItem('testItem2');
    }

    /**
     * @throws \Exception
     */
    public function testHasKey()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_NO_CHANGE);
        $this->storage->setItem('testItem1', 'testValue1');
        $this->assertTrue($this->storage->hasItem('testItem1'));
        $this->assertFalse($this->storage->hasItem('testItem2'));
    }

    /**
     * @throws \Exception
     */
    public function testGetItems()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_NO_CHANGE);
        $data = ['testItem1' => 'testValue1', 'testItem2' => 'testValue2'];

        $this->storage->setItems($data);
        $this->assertEquals($data, $this->storage->getItems());
    }

    /**
     * @throws Exception
     */
    public function testGetOnly()
    {
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_NO_CHANGE);
        $data = ['testItem1' => 'testValue1', 'testItem2' => 'testValue2', 'testItem3' => 'testValue3'];

        $this->storage->setItems($data);
        $this->assertEquals(
            ['testItem2' => 'testValue2', 'testItem3' => 'testValue3'],
            $this->storage->getOnly(['testItem3', 'testItem2']),
            'Simple case'
        );

        $this->assertEquals(
            ['testItem3' => 'testValue3', 'testItem2' => 'testValue2'],
            $this->storage->getOnly(['testItem2', 'testItem3'], true),
            'Respect keys test'
        );

        $this->assertEquals(
            ['testItem3' => 'testValue3'],
            $this->storage->getOnly(['testItem3', 'testItem4'], true, false),
            'Respect keys with an absent key'
        );
    }

    /**
     * @throws Exception
     */
    public function testGetOnlyException()
    {
        $data = ['testItem1' => 'testValue1', 'testItem2' => 'testValue2', 'testItem3' => 'testValue3'];
        $this->storage->setItems($data);

        $this->setExpectedException(Exception::class);
        $this->assertEquals(
            ['testItem3' => 'testValue3'],
            $this->storage->getOnly(['testItem3', 'testItem4'], true, true)
        );
    }

    /**
     * @throws Exception
     */
    public function testGetOnlyValues()
    {
        $data = ['testItem1' => 'testValue1', 'testItem2' => 'testValue2', 'testItem3' => 'testValue3'];

        $this->storage->setItems($data);

        $this->assertEquals(['testValue1', 'testValue2'], $this->storage->getOnlyValues(['testItem1', 'testItem2']));
    }

    /**
     * @throws Exception
     */
    public function testGetExcept()
    {
        $data = ['testItem1' => 'testValue1', 'testItem2' => 'testValue2', 'testItem3' => 'testValue3'];
        $expected = ['testItem1' => 'testValue1', 'testItem3' => 'testValue3'];

        $this->storage->setItems($data);

        $this->assertEquals($expected, $this->storage->getExcept(['testItem2']));
    }

    /**
     * @throws Exception
     */
    public function testGetIterator()
    {
        $this->storage->setItems(['testItem1' => 'testValue1']);

        $this->assertInstanceOf(\ArrayIterator::class, $this->storage->getIterator());
        $this->assertEquals('testValue1', $this->storage->getIterator()->offsetGet('testItem1'));
    }

    /**
     * @throws Exception
     */
    public function testNullValues()
    {
        $this->storage->setItems(['testItem1' => null]);
        $this->assertTrue($this->storage->hasItem('testItem1'));
        $this->assertFalse($this->storage->__isset('testItem1'));
    }

    /**
     * @throws Exception
     */
    public function testUnset()
    {
        $this->storage->setItems(['testItem1' => 'testValue1', 'testItem2' => 'testValue2']);
        $this->storage->unsetItem('testItem1');
        $this->assertEquals(['testItem2' => 'testValue2'], $this->storage->getItems());
    }

    /**
     * @throws Exception
     */
    public function testSetKeyNormalizerException()
    {
        $this->storage->setItems(['testItem1' => 'testValue1', 'testItem2' => 'testValue2']);
        $this->setExpectedException(Exception::class);
        $this->storage->setKeyNormalizer(ArrayDataStorage::KEY_CASE_NO_CHANGE);
    }
}
