<?php

namespace BlueEvent\Event\Base;

use SimpleLog\Log;

class EventLog
{
    /**
     * store all event names to log
     *
     * @var array
     */
    public $logEvents = [];

    /**
     * store logger instance
     *
     * @var \SimpleLog\LogInterface
     */
    protected $loggerInstance;

    /**
     * store default options for event dispatcher
     *
     * @var array
     */
    protected $options = [];

    /**
     * EventLog constructor.
     *
     * @param array $logConfig
     */
    public function __construct(array $logConfig)
    {
        $this->options = $logConfig;

        if ($this->options['log_object']
            && $this->options['log_object'] instanceof \SimpleLog\LogInterface
        ) {
            $this->loggerInstance = $this->options['log_object'];
        } else {
            $this->loggerInstance = new Log($this->options['log_config']);
        }
    }

    /**
     * check that event data can be logged and create log message
     *
     * @param string $name
     * @param mixed $eventListener
     * @param bool|string $status
     * @return $this
     */
    public function makeLogEvent($name, $eventListener, $status)
    {
        if ($this->options['log_events']
            && ($this->options['log_all_events']
                || in_array($name, $this->logEvents, true)
            )
        ) {
            $this->loggerInstance->makeLog(
                [
                    'event_name' => $name,
                    'listener' => $this->getListenerData($eventListener),
                    'status' => $status
                ]
            );
        }

        return $this;
    }

    /**
     * @param mixed $eventListener
     * @return string
     */
    protected function getListenerData($eventListener)
    {
        switch (true) {
            case $eventListener instanceof \Closure:
                return 'Closure';

            case is_array($eventListener):
                return $eventListener[0] . '::' . $eventListener[1];

            default:
                return $eventListener;
        }
    }
}
