<?php

namespace FHTeam\LaravelValidator\Utility;

use Exception;

/**
 * Class Arr
 *
 * @package FHTeam\LaravelValidator\Utility
 */
class Arr extends \Illuminate\Support\Arr
{
    /**
     * Get a subset of the items from the given array while respecting order.
     * The order of the keys which to return is taken from the $keys parameter
     *
     * @param  array        $array
     * @param  array|string $keys
     *
     * @return array
     */
    public static function onlyRespectOrder(array $array, $keys)
    {
        //TODO: optimize
        $flippedKeys = array_flip($keys);

        $returnPlaceHolder = array_intersect_key($flippedKeys, $array);
        $array = array_intersect_key($array, $flippedKeys);

        return array_replace($returnPlaceHolder, $array);
    }

    /**
     * Returns data from a 'conditional' array of the following format
     * $data = [
     *      ['*'] =>
     *          ['field1' => 'ruleData1',
     *           'field2' => 'ruleData2', ], //Global rules
     *      ['condition1, condition2'] =>
     *          ['field1' => 'ruleData1',
     *           'field2' => 'ruleData2', ] //Conditional ones
     * All the data matching conditions is merged upon returning
     *
     * @param array       $data
     * @param string|null $condition
     * @param bool        $allowEmpty
     *
     * @return array
     * @throws Exception
     */
    public static function mergeByCondition(array $data, $condition = null, $allowEmpty = false)
    {
        if (null == $condition) {
            return $data;
        }

        $result = [];

        // Fetching wildcard options first
        if (isset($data['*'])) {
            $result = $data['*'];
        }

        foreach ($data as $actions => $rules) {
            $actions = array_map('trim', explode(',', $actions));
            foreach ($actions as $action) {
                if ($action == $condition) {
                    $result = array_merge($result, (array)$rules);
                }
            }
        }

        if (empty($result) && !$allowEmpty && !isset($data[$condition])) {
            throw new Exception("Nothing matched condition '$condition' among ".json_encode(array_keys($data)));
        }

        return $result;
    }
}
