<?php


namespace FHTeam\LaravelValidator\Tests\Utility;


use Exception;
use FHTeam\LaravelValidator\Tests\TestBase;
use FHTeam\LaravelValidator\Utility\Arr;

/**
 * Class ArrTest
 *
 * @package FHTeam\LaravelValidator\Test\Utility
 */
class ArrTest extends TestBase
{
    protected $testData = [
        '*'                      => [
            'sharedKey1' => 'sharedValue1',
            'sharedKey2' => 'sharedValue2',
        ],
        'masterKey1'             => [
            'MasterKey11' => 'MasterKeyValue11',
            'MasterKey12' => 'MasterKeyValue12',
        ],
        'masterKey2, masterKey3' => [
            'MasterKey231' => 'MasterKeyValue231',
            'MasterKey232' => 'MasterKeyValue232',
        ],
    ];

    /**
     *
     */
    public function testOnlyRespectOrder()
    {
        $testData = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];

        $expected = ['key3' => 'value3', 'key2' => 'value2'];

        $this->assertEquals(
            $expected,
            Arr::onlyRespectOrder($testData, ['key3', 'key2'])
        );
    }

    /**
     * @throws \Exception
     */
    public function testMergeByConditionNotExists()
    {
        $expected = [
            'sharedKey1' => 'sharedValue1',
            'sharedKey2' => 'sharedValue2',
        ];

        $this->handleExceptions([Exception::class]);
        try {
            Arr::mergeByCondition($expected, 'absent');
        } catch (\Exception $e) {
        }
    }

    public function testMergeByConditionSimple()
    {
        $expected = [
            'sharedKey1'  => 'sharedValue1',
            'sharedKey2'  => 'sharedValue2',
            'MasterKey11' => 'MasterKeyValue11',
            'MasterKey12' => 'MasterKeyValue12',
        ];

        $this->assertEquals($expected, Arr::mergeByCondition($this->testData, 'masterKey1'));
    }

    /**
     * @throws \Exception
     */
    public function testMergeByConditionComplex1()
    {
        $expected = [
            'sharedKey1'   => 'sharedValue1',
            'sharedKey2'   => 'sharedValue2',
            'MasterKey231' => 'MasterKeyValue231',
            'MasterKey232' => 'MasterKeyValue232',
        ];

        $this->assertEquals($expected, Arr::mergeByCondition($this->testData, 'masterKey2'));
        $this->assertEquals($expected, Arr::mergeByCondition($this->testData, 'masterKey3'));
    }
}
