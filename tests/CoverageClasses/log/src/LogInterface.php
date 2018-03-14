<?php

namespace SimpleLog;

interface LogInterface
{
    /**
     * create log message
     *
     * @param array|string|object $message
     * @param array $context
     * @return $this
     */
    public function makeLog($message, array $context = []);

    /**
     * set log option for all future executions of makeLog
     *
     * @param string $key
     * @param mixed $val
     * @return $this
     */
    public function setOption($key, $val);

    /**
     * return all configuration or only given key value
     *
     * @param null|string $key
     * @return array|mixed
     */
    public function getOption($key = null);
}
