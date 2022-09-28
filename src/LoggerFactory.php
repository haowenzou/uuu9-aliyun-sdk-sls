<?php

namespace AliyunSLS;
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

/**
 * Class LoggerFactory
 * Factory for creating logger instance, with $client, $project, $logstore, $topic configurable.
 * Will flush current logger when the factory instance was recycled.
 */
class LoggerFactory
{

    private static $loggerMap = array();

    /**
     * set modifier to protected for singleton pattern
     * Aliyun_Log_LoggerFactory constructor.
     */
    protected function __construct()
    {

    }

    /**
     * Get logger instance
     * @param $client
     * @param $project
     * @param $logstore
     * @param null $topic
     * @return mixed
     * @throws \Exception
     */
    public static function getLogger($client, $project, $logstore, $topic = null)
    {
        if ($project === null || $project == '') {
            throw new \Exception('project name is blank!');
        }
        if ($logstore === null || $logstore == '') {
            throw new \Exception('logstore name is blank!');
        }
        if ($topic === null) {
            $topic = '';
        }
        $loggerKey = $project . '#' . $logstore . '#' . $topic;
        if (!array_key_exists($loggerKey, static::$loggerMap)) {
            $instanceSimpleLogger = new SimpleLogger($client, $project, $logstore, $topic);
            static::$loggerMap[$loggerKey] = $instanceSimpleLogger;
        }
        return static::$loggerMap[$loggerKey];
    }

    /**
     * flush current logger in destruct function
     */
    function __destruct()
    {
        if (static::$loggerMap != null) {
            foreach (static::$loggerMap as $innerLogger) {
                $innerLogger->logFlush();
            }
        }
    }

    /**
     * set clone function to private for singleton pattern
     */
    private function __clone()
    {
    }
}
