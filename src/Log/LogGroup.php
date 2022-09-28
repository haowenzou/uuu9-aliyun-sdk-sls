<?php
/**
 * Created by PhpStorm.
 * User: zhaotong
 * Date: 2019-03-09
 * Time: 13:39
 */

namespace AliyunSLS\Log;


use AliyunSLS\Protobuf\Protobuf;

/**
 * Class LogGroup
 * @package AliyunSLS
 */
class LogGroup
{
    /**
     * @var
     */
    private $_unknown;
    /**
     * @var null
     */
    private $logs_ = null;
    /**
     * @var null
     */
    private $reserved_ = null;
    /**
     * @var null
     */
    private $topic_ = null;
    /**
     * @var null
     */
    private $source_ = null;

    /**
     * LogGroup constructor.
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
            //var_dump("LogGroup: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
            switch ($field) {
                case 1:
                    ASSERT('$wire == 2');
                    $len = Protobuf::read_varint($fp, $limit);
                    if ($len === false)
                        throw new \Exception('Protobuf::read_varint returned false');
                    $limit -= $len;
                    $this->logs_[] = new Log($fp, $len);
                    ASSERT('$len == 0');
                    break;
                case 2:
                    ASSERT('$wire == 2');
                    $len = Protobuf::read_varint($fp, $limit);
                    if ($len === false)
                        throw new \Exception('Protobuf::read_varint returned false');
                    if ($len > 0)
                        $tmp = fread($fp, $len);
                    else
                        $tmp = '';
                    if ($tmp === false)
                        throw new \Exception("fread($len) returned false");
                    $this->reserved_ = $tmp;
                    $limit -= $len;
                    break;
                case 3:
                    ASSERT('$wire == 2');
                    $len = Protobuf::read_varint($fp, $limit);
                    if ($len === false)
                        throw new \Exception('Protobuf::read_varint returned false');
                    if ($len > 0)
                        $tmp = fread($fp, $len);
                    else
                        $tmp = '';
                    if ($tmp === false)
                        throw new \Exception("fread($len) returned false");
                    $this->topic_ = $tmp;
                    $limit -= $len;
                    break;
                case 4:
                    ASSERT('$wire == 2');
                    $len = Protobuf::read_varint($fp, $limit);
                    if ($len === false)
                        throw new \Exception('Protobuf::read_varint returned false');
                    if ($len > 0)
                        $tmp = fread($fp, $len);
                    else
                        $tmp = '';
                    if ($tmp === false)
                        throw new \Exception("fread($len) returned false");
                    $this->source_ = $tmp;
                    $limit -= $len;
                    break;
                default:
                    $this->_unknown[$field . '-' . Protobuf::get_wiretype($wire)][] = Protobuf::read_field($fp, $wire, $limit);
            }
        }
        if (!$this->validateRequired())
            throw new \Exception('Required fields are missing');
    }

    // repeated .Log logs = 1;

    /**
     * @return bool
     */
    public function validateRequired()
    {
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
        if (!is_null($this->logs_))
            foreach ($this->logs_ as $v) {
                fwrite($fp, "\x0a");
                Protobuf::write_varint($fp, $v->size()); // message
                $v->write($fp);
            }
        if (!is_null($this->reserved_)) {
            fwrite($fp, "\x12");
            Protobuf::write_varint($fp, strlen($this->reserved_));
            fwrite($fp, $this->reserved_);
        }
        if (!is_null($this->topic_)) {
            fwrite($fp, "\x1a");
            Protobuf::write_varint($fp, strlen($this->topic_));
            fwrite($fp, $this->topic_);
        }
        if (!is_null($this->source_)) {
            fwrite($fp, "\"");
            Protobuf::write_varint($fp, strlen($this->source_));
            fwrite($fp, $this->source_);
        }
    }

    /**
     * @return int
     */
    public function size()
    {
        $size = 0;
        if (!is_null($this->logs_))
            foreach ($this->logs_ as $v) {
                $l = $v->size();
                $size += 1 + Protobuf::size_varint($l) + $l;
            }
        if (!is_null($this->reserved_)) {
            $l = strlen($this->reserved_);
            $size += 1 + Protobuf::size_varint($l) + $l;
        }
        if (!is_null($this->topic_)) {
            $l = strlen($this->topic_);
            $size += 1 + Protobuf::size_varint($l) + $l;
        }
        if (!is_null($this->source_)) {
            $l = strlen($this->source_);
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
            . Protobuf::toString('logs_', $this->logs_)
            . Protobuf::toString('reserved_', $this->reserved_)
            . Protobuf::toString('topic_', $this->topic_)
            . Protobuf::toString('source_', $this->source_);
    }

    /**
     *
     */
    public function clearLogs()
    {
        $this->logs_ = null;
    }

    /**
     * @return int
     */
    public function getLogsCount()
    {
        if ($this->logs_ === null) return 0; else return count($this->logs_);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function getLogs($index)
    {
        return $this->logs_[$index];
    }

    /**
     * @param $index
     * @param $value
     */
    public function setLogs($index, $value)
    {
        $this->logs_[$index] = $value;
    }

    // optional string reserved = 2;

    /**
     * @return array|null
     */
    public function getLogsArray()
    {
        if ($this->logs_ === null) return array(); else return $this->logs_;
    }

    /**
     * @param $value
     */
    public function addLogs($value)
    {
        $this->logs_[] = $value;
    }

    /**
     * @param array $values
     */
    public function addAllLogs(array $values)
    {
        foreach ($values as $value) {
            $this->logs_[] = $value;
        }
    }

    /**
     *
     */
    public function clearReserved()
    {
        $this->reserved_ = null;
    }

    /**
     * @return bool
     */
    public function hasReserved()
    {
        return $this->reserved_ !== null;
    }

    // optional string topic = 3;

    /**
     * @return string|null
     */
    public function getReserved()
    {
        if ($this->reserved_ === null) return ""; else return $this->reserved_;
    }

    /**
     * @param $value
     */
    public function setReserved($value)
    {
        $this->reserved_ = $value;
    }

    /**
     *
     */
    public function clearTopic()
    {
        $this->topic_ = null;
    }

    /**
     * @return bool
     */
    public function hasTopic()
    {
        return $this->topic_ !== null;
    }

    /**
     * @return string|null
     */
    public function getTopic()
    {
        if ($this->topic_ === null) return ""; else return $this->topic_;
    }

    // optional string source = 4;

    /**
     * @param $value
     */
    public function setTopic($value)
    {
        $this->topic_ = $value;
    }

    /**
     *
     */
    public function clearSource()
    {
        $this->source_ = null;
    }

    /**
     * @return bool
     */
    public function hasSource()
    {
        return $this->source_ !== null;
    }

    /**
     * @return string|null
     */
    public function getSource()
    {
        if ($this->source_ === null) return ""; else return $this->source_;
    }

    /**
     * @param $value
     */
    public function setSource($value)
    {
        $this->source_ = $value;
    }

    // @@protoc_insertion_point(class_scope:LogGroup)
}