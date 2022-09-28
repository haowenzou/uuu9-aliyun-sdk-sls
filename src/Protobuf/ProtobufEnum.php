<?php
/**
 * Created by PhpStorm.
 * User: zhaotong
 * Date: 2019-03-09
 * Time: 13:36
 */

namespace AliyunSLS\Protobuf;


/**
 * Class ProtobufEnum
 * @package AliyunSLS
 */
class ProtobufEnum
{

    /**
     * @param $value
     * @return string|null
     */
    public static function toString($value)
    {
        if (is_null($value))
            return null;
        if (array_key_exists($value, self::$_values))
            return self::$_values[$value];
        return 'UNKNOWN';
    }
}