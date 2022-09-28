<?php
/**
 * Created by PhpStorm.
 * User: zhaotong
 * Date: 2019-03-09
 * Time: 13:39
 */

namespace AliyunSLS\Log;

use AliyunSLS\LogContent;
use AliyunSLS\Protobuf\Protobuf;

/**
 * Class Log
 * @package AliyunSLS
 */
class Log
{
    /**
     * @var
     */
    private $_unknown;
    /**
     * @var null
     */
    private $time_ = null;
    /**
     * @var null
     */
    private $contents_ = null;

    /**
     * Log constructor.
     * @param null $in
     * @param int $limit
     * @throws \Exception
     */
    function __construct($in = NULL, &$limit = PHP_INT_MAX)
    {
        if ($in !== NULL) {
            if (is_string($in)) {
                $fp = fopen('php://memory', 'r+b');
                fwrite($fp, $in);
                rewind($fp);
            } else if (is_resource($in)) {
                $fp = $in;
            } else {
                throw new \Exception('Invalid in parameter');
            }
            $this->read($fp, $limit);
        }
    }

    /**
     * @param $fp
     * @param int $limit
     * @throws \Exception
     */
    function read($fp, &$limit = PHP_INT_MAX)
    {
        while (!feof($fp) && $limit > 0) {
            $tag = Protobuf::read_varint($fp, $limit);
            if ($tag === false) break;
            $wire = $tag & 0x07;
            $field = $tag >> 3;
            //var_dump("Log: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
            switch ($field) {
                case 1:
                    ASSERT('$wire == 0');
                    $tmp = Protobuf::read_varint($fp, $limit);
                    if ($tmp === false)
                        throw new \Exception('Protobuf::read_varint returned false');
                    $this->time_ = $tmp;

                    break;
                case 2:
                    ASSERT('$wire == 2');
                    $len = Protobuf::read_varint($fp, $limit);
                    if ($len === false)
                        throw new \Exception('Protobuf::read_varint returned false');
                    $limit -= $len;
                    $this->contents_[] = new LogContent($fp, $len);
                    ASSERT('$len == 0');
                    break;
                default:
                    $this->_unknown[$field . '-' . Protobuf::get_wiretype($wire)][] = Protobuf::read_field($fp, $wire, $limit);
            }
        }
        if (!$this->validateRequired())
            throw new \Exception('Required fields are missing');
    }

    /**
     * @return bool
     */
    public function validateRequired()
    {
        if ($this->time_ === null) return false;
        return true;
    }

    /**
     * @param $fp
     * @throws \Exception
     */
    function write($fp)
    {
        if (!$this->validateRequired())
            throw new \Exception('Required fields are missing');
        if (!is_null($this->time_)) {
            fwrite($fp, "\x08");
            Protobuf::write_varint($fp, $this->time_);
        }
        if (!is_null($this->contents_))
            foreach ($this->contents_ as $v) {
                fwrite($fp, "\x12");
                Protobuf::write_varint($fp, $v->size()); // message
                $v->write($fp);
            }
    }

    // required uint32 time = 1;

    /**
     * @return int
     */
    public function size()
    {
        $size = 0;
        if (!is_null($this->time_)) {
            $size += 1 + Protobuf::size_varint($this->time_);
        }
        if (!is_null($this->contents_))
            foreach ($this->contents_ as $v) {
                $l = $v->size();
                $size += 1 + Protobuf::size_varint($l) + $l;
            }
        return $size;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ''
            . Protobuf::toString('unknown', $this->_unknown)
            . Protobuf::toString('time_', $this->time_)
            . Protobuf::toString('contents_', $this->contents_);
    }

    /**
     *
     */
    public function clearTime()
    {
        $this->time_ = null;
    }

    /**
     * @return bool
     */
    public function hasTime()
    {
        return $this->time_ !== null;
    }

    /**
     * @return int|null
     */
    public function getTime()
    {
        if ($this->time_ === null) return 0; else return $this->time_;
    }

    // repeated .Log.Content contents = 2;

    /**
     * @param $value
     */
    public function setTime($value)
    {
        $this->time_ = $value;
    }

    /**
     *
     */
    public function clearContents()
    {
        $this->contents_ = null;
    }

    /**
     * @return int
     */
    public function getContentsCount()
    {
        if ($this->contents_ === null) return 0; else return count($this->contents_);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function getContents($index)
    {
        return $this->contents_[$index];
    }

    /**
     * @param $index
     * @param $value
     */
    public function setContents($index, $value)
    {
        $this->contents_[$index] = $value;
    }

    /**
     * @return array|null
     */
    public function getContentsArray()
    {
        if ($this->contents_ === null) return array(); else return $this->contents_;
    }

    /**
     * @param $value
     */
    public function addContents($value)
    {
        $this->contents_[] = $value;
    }

    /**
     * @param array $values
     */
    public function addAllContents(array $values)
    {
        foreach ($values as $value) {
            $this->contents_[] = $value;
        }
    }

    // @@protoc_insertion_point(class_scope:Log)
}