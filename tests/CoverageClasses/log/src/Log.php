<?php

namespace SimpleLog;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;
use SimpleLog\Storage\StorageInterface;
use SimpleLog\Message\MessageInterface;

class Log implements LogInterface, LoggerInterface
{
    use LoggerTrait;

    /**
     * @var array
     */
    protected $defaultParams = [
        'log_path' => './log',
        'level' => 'notice',
        'storage' => \SimpleLog\Storage\File::class,
        'message' => \SimpleLog\Message\DefaultMessage::class,
    ];

    /**
     * @var \SimpleLog\Storage\StorageInterface
     */
    protected $storage;

    /**
     * @var array
     */
    protected $levels = [];

    /**
     * @var \SimpleLog\Message\MessageInterface
     */
    protected $message;

    /**
     * @param array $params
     * @throws \ReflectionException
     */
    public function __construct(array $params = [])
    {
        $this->defaultParams = array_merge($this->defaultParams, $params);

        $levels = new \ReflectionClass(new LogLevel);
        $this->levels = $levels->getConstants();

        $this->reloadStorage();
        $this->reloadMessage();
    }

    /**
     * log event information into file
     *
     * @param array|string|object $message
     * @param array $context
     * @return $this
     */
    public function makeLog($message, array $context = [])
    {
        $this->log($this->defaultParams['level'], $message, $context);

        return $this;
    }

    /**
     * @param string $level
     * @param string|array|object $message
     * @param array $context
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, $message, array $context = [])
    {
        if (!in_array($level, $this->levels, true)) {
            throw new InvalidArgumentException('Level not defined: ' . $level);
        }

        $newMessage = $this->message
            ->createMessage($message, $context)
            ->getMessage();

        $this->storage->store($newMessage, $level);
    }

    /**
     * @return $this
     */
    protected function reloadStorage()
    {
        if ($this->defaultParams['storage'] instanceof StorageInterface) {
            $this->storage = $this->defaultParams['storage'];
            return $this;
        }

        $this->storage = new $this->defaultParams['storage']($this->defaultParams);
        return $this;
    }

    /**
     * @return $this
     */
    protected function reloadMessage()
    {
        if ($this->defaultParams['message'] instanceof MessageInterface) {
            $this->message = $this->defaultParams['message'];
            return $this;
        }

        $this->message = new $this->defaultParams['message']($this->defaultParams);
        return $this;
    }

    /**
     * set log option for all future executions of makeLog
     *
     * @param string $key
     * @param mixed $val
     * @return $this
     */
    public function setOption($key, $val)
    {
        $this->defaultParams[$key] = $val;
        return $this->reloadStorage();
    }

    /**
     * return all configuration or only given key value
     *
     * @param null|string $key
     * @return array|mixed
     */
    public function getOption($key = null)
    {
        if (is_null($key)) {
            return $this->defaultParams;
        }

        return $this->defaultParams[$key];
    }

    /**
     * @return string
     */
    public function getLastMessage()
    {
        return $this->message->getMessage();
    }
}
