<?php

namespace SimpleLog;

class LogStatic
{
    /**
     * @var Log
     */
    protected static $instance;

    /**
     * log event information into file
     *
     * @param string $level
     * @param array|string $message
     * @param array $context
     * @param array $params
     */
    public static function log($level, $message, array $context = [], array $params = [])
    {
        self::init($params);
        self::$instance->log($level, $message, $context);
    }

    /**
     * log event information into file
     *
     * @param array|string $message
     * @param array $context
     * @param array $params
     */
    public static function makeLog($message, array $context = [], array $params = [])
    {
        self::init($params);
        self::$instance->makeLog($message, $context);
    }

    /**
     * set log option for all future executions of makeLog
     *
     * @param string $key
     * @param mixed $val
     * @return Log
     */
    public static function setOption($key, $val)
    {
        self::init();
        return self::$instance->setOption($key, $val);
    }

    /**
     * return all configuration or only given key value
     *
     * @param null|string $key
     * @return array|mixed
     */
    public static function getOption($key = null)
    {
        self::init();
        return self::$instance->getOption($key);
    }

    /**
     * create Log object if not exists
     *
     * @param array $params
     */
    protected static function init(array $params = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new Log($params);
        }
    }
}
