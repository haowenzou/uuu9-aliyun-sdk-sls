<?php

namespace AliyunSLS;

use AliyunSLS\Models\LogItem;
use AliyunSLS\Models\LogLevel\LogLevel;
use AliyunSLS\Models\Request\PutLogsRequest;

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

/**
 * Class Aliyun_Log_SimpleLogger
 * a wrapper for submit log message to server, to avoid post log frequently, using a internal cache for messages
 * When the count of messages reach the cache size, SimpleLogger will post the messages in bulk, and reset the cache accordingly.
 */
class SimpleLogger
{

    /**
     * internal cache for log messages
     * @var array
     */
    private $logItems = [];

    /**
     * max size of cached messages
     * @var int
     */
    private $maxCacheLog;

    /**
     * log topic field
     * @var
     */
    private $topic;

    /**
     * max time before logger post the cached messages
     * @var int
     */
    private $maxWaitTime;

    /**
     * previous time for posting log messages
     * @var int
     */
    private $previousLogTime;

    /**
     * max storage size for cached messages
     * @var int
     */
    private $maxCacheBytes;

    /**
     * messages storage size for cached messages
     * @var int
     */
    private $cacheBytes;

    /**
     * log client which was wrappered by this logger
     * @var log
     */
    private $client;

    /**
     * log project name
     * @var $project
     */
    private $project;

    /**
     * @var $logstore
     */
    private $logstore;

    /**
     * Aliyun_Log_SimpleLogger constructor.
     * @param $client
     * @param $project
     * @param $logstore
     * @param $topic
     * @param null $maxCacheLog
     * @param null $maxWaitTime
     * @param null $maxCacheBytes
     * @throws \Exception
     */
    public function __construct($client, $project, $logstore, $topic, $maxCacheLog = null, $maxWaitTime = null, $maxCacheBytes = null)
    {
        if (NULL === $maxCacheLog || !is_integer($maxCacheLog)) {
            $this->maxCacheLog = 100;
        } else {
            $this->maxCacheLog = $maxCacheLog;
        }

        if (NULL === $maxCacheBytes || !is_integer($maxCacheBytes)) {
            $this->maxCacheBytes = 256 * 1024;
        } else {
            $this->maxCacheBytes = $maxCacheBytes;
        }

        if (NULL === $maxWaitTime || !is_integer($maxWaitTime)) {
            $this->maxWaitTime = 5;
        } else {
            $this->maxWaitTime = $maxWaitTime;
        }
        if ($client == null || $project == null || $logstore == null) {
            throw new \Exception('the input parameter is invalid! create SimpleLogger failed!');
        }
        $this->client = $client;
        $this->project = $project;
        $this->logstore = $logstore;
        $this->topic = $topic;
        $this->previousLogTime = time();
        $this->cacheBytes = 0;
    }

    /**
     * submit string log message with info level
     * @param $logMessage
     * @throws \Exception
     */
    public function info($logMessage)
    {
        $logLevel = LogLevel::getLevelInfo();
        $this->logSingleMessage($logLevel, $logMessage);
    }

    /**
     * log single string message
     * @param LogLevel $logLevel
     * @param $logMessage
     * @throws \Exception
     */
    private function logSingleMessage(LogLevel $logLevel, $logMessage)
    {
        if (is_array($logMessage)) {
            throw new \Exception('array is not supported in this function, please use logArrayMessage!');
        }
        $cur_time = time();
        $contents = array( // key-value pair
            'time' => date('m/d/Y h:i:s a', $cur_time),
            'loglevel' => LogLevel::getLevelStr($logLevel),
            'msg' => $logMessage
        );
        $this->cacheBytes += strlen($logMessage) + 32;
        $logItem = new LogItem();
        $logItem->setTime($cur_time);
        $logItem->setContents($contents);
        $this->logItem($cur_time, $logItem);
    }

    /**
     * add logItem to cached array, and post the cached messages when cache reach the limitation
     * @param $cur_time
     * @param $logItem
     */
    private function logItem($cur_time, $logItem)
    {
        array_push($this->logItems, $logItem);
        if ($cur_time - $this->previousLogTime >= $this->maxWaitTime || sizeof($this->logItems) >= $this->maxCacheLog
            || $this->cacheBytes >= $this->maxCacheBytes) {
            $this->logBatch($this->logItems, $this->topic);
            $this->logItems = [];
            $this->previousLogTime = time();
            $this->cacheBytes = 0;
        }
    }

    /**
     * submit log messages in bulk
     * @param $logItems
     * @param $topic
     */
    private function logBatch($logItems, $topic)
    {
        $ip = $this->getLocalIp();
        $request = new PutLogsRequest($this->project, $this->logstore,
            $topic, $ip, $logItems);
        $error_exception = NULL;
        for ($i = 0; $i < 3; $i++) {
            try {
                $response = $this->client->putLogs($request);
                return;
            } catch (Exception $ex) {
                $error_exception = $ex;
            } catch (\Exception $ex) {
                $error_exception = $ex;
            }
        }
        if ($error_exception != NULL) {
        }
    }

    /**
     * get current machine IP
     * @return string
     */
    private function getLocalIp()
    {
        $local_ip = getHostByName(php_uname('n'));
        if (strlen($local_ip) == 0) {
            $local_ip = getHostByName(getHostName());
        }
        return $local_ip;
    }

    /**
     * submit string log message with debug level
     * @param $logMessage
     * @throws \Exception
     */
    public function debug($logMessage)
    {
        $logLevel = LogLevel::getLevelDebug();
        $this->logSingleMessage($logLevel, $logMessage);
    }

    /**
     * submit string log message with warn level
     * @param $logMessage
     * @throws \Exception
     */
    public function warn($logMessage)
    {
        $logLevel = LogLevel::getLevelWarn();
        $this->logSingleMessage($logLevel, $logMessage);
    }

    /**
     * submit string log message with error level
     * @param $logMessage
     * @throws \Exception
     */
    public function error($logMessage)
    {
        $logLevel = LogLevel::getLevelError();
        $this->logSingleMessage($logLevel, $logMessage);
    }

    /**
     * submit array log message with info level
     * @param $logMessage
     * @throws \Exception
     */
    public function infoArray($logMessage)
    {
        $logLevel = LogLevel::getLevelInfo();
        $this->logArrayMessage($logLevel, $logMessage);
    }

    /**
     * log array message
     * @param LogLevel $logLevel
     * @param $logMessage
     * @throws \Exception
     */
    private function logArrayMessage(LogLevel $logLevel, $logMessage)
    {
        if (!is_array($logMessage)) {
            throw new \Exception('input message is not array, please use logSingleMessage!');
        }
        $cur_time = time();
        $contents = array( // key-value pair
            'time' => date('m/d/Y h:i:s a', $cur_time)
        );
        $contents['logLevel'] = LogLevel::getLevelStr($logLevel);
        foreach ($logMessage as $key => $value) {
            $contents[$key] = $value;
            $this->cacheBytes += strlen($key) + strlen($value);
        }
        $this->cacheBytes += 32;
        $logItem = new LogItem();
        $logItem->setTime($cur_time);
        $logItem->setContents($contents);
        $this->logItem($cur_time, $logItem);
    }

    /**
     * submit array log message with debug level
     * @param $logMessage
     * @throws \Exception
     */
    public function debugArray($logMessage)
    {
        $logLevel = LogLevel::getLevelDebug();
        $this->logArrayMessage($logLevel, $logMessage);
    }

    /**
     * submit array log message with warn level
     * @param $logMessage
     * @throws \Exception
     */
    public function warnArray($logMessage)
    {
        $logLevel = LogLevel::getLevelWarn();
        $this->logArrayMessage($logLevel, $logMessage);
    }

    /**
     * submit array log message with error level
     * @param $logMessage
     * @throws \Exception
     */
    public function errorArray($logMessage)
    {
        $logLevel = LogLevel::getLevelError();
        $this->logArrayMessage($logLevel, $logMessage);
    }

    /**
     * manually flush all cached log to log server
     */
    public function logFlush()
    {
        if (sizeof($this->logItems) > 0) {
            $this->logBatch($this->logItems, $this->topic);
            $this->logItems = [];
            $this->previousLogTime = time();
            $this->cacheBytes = 0;
        }
    }

    function __destruct()
    {
        if (sizeof($this->logItems) > 0) {
            $this->logBatch($this->logItems, $this->topic);
        }
    }
}
