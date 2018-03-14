<?php
/**
 * Event Dispatcher Class
 *
 * @package     BlueEvent
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 */

namespace BlueEvent\Event\Base;

use BlueEvent\Event\Base\Interfaces\EventDispatcherInterface;
use BlueEvent\Event\Base\Interfaces\EventInterface;
use BlueEvent\Event\BaseEvent;

class EventDispatcher implements EventDispatcherInterface
{
    const EVENT_STATUS_OK       = 'ok';
    const EVENT_STATUS_ERROR    = 'error';
    const EVENT_STATUS_BREAK    = 'propagation_stop';

    /**
     * @var bool
     */
    protected $hasErrors = false;

    /**
     * store all errors
     *
     * @var
     */
    protected $errorList = [];

    /**
     * store logger instance
     *
     * @var EventLog
     */
    protected $loggerInstance;

    /**
     * store default options for event dispatcher
     *
     * @var array
     */
    protected $options = [
        'type' => 'array',
        'log_events' => false,
        'log_all_events' => true,
        'from_file' => false,
        'log_object' => false,
        'log_config' => [
            'log_path' => './log',
            'level' => 'debug',
            'storage' => \SimpleLog\Storage\File::class,
        ],
        'events' => [],
    ];

    /**
     * create manage instance
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options = [])
    {
        $this->options = array_replace_recursive($this->options, $options);

        if ($this->options['from_file']) {
            $this->readEventConfiguration(
                $this->options['from_file'],
                $this->options['type']
            );
        }

        $this->loggerInstance = new EventLog($this->options);
    }

    /**
     * return event object or create it if not exist
     *
     * @param string $eventName
     * @param array $data
     * @return EventInterface
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    protected function createEventObject($eventName, array $data)
    {
        if (!array_key_exists($eventName, $this->options['events'])) {
            throw new \InvalidArgumentException('Event is not defined.');
        }

        $namespace = $this->options['events'][$eventName]['object'];
        $instance = new $namespace($eventName, $data);

        if (!($instance instanceof EventInterface)) {
            throw new \LogicException('Invalid interface of event object');
        }

        return $instance;
    }

    /**
     * add event configuration into event dispatcher
     *
     * @param array $events
     * @return $this
     */
    public function setEventConfiguration(array $events)
    {
        $this->options['events'] = array_merge_recursive(
            $this->options['events'],
            $events
        );

        return $this;
    }

    /**
     * trigger new event with automatic call all subscribed listeners
     *
     * @param string $name
     * @param array $data
     * @return $this
     * @throws \LogicException
     */
    public function triggerEvent($name, array $data = [])
    {
        try {
            /** @var EventInterface $event */
            $event = $this->createEventObject($name, $data);
        } catch (\InvalidArgumentException $exception) {
            return $this;
        }

        foreach ($this->options['events'][$name]['listeners'] as $eventListener) {
            if ($event->isPropagationStopped()) {
                $this->loggerInstance->makeLogEvent($name, $eventListener, self::EVENT_STATUS_BREAK);
                break;
            }

            $this->executeListener($eventListener, $event, $name);
        }

        return $this;
    }

    /**
     * @param mixed $eventListener
     * @param EventInterface $event
     * @param string $name
     * @return $this
     */
    protected function executeListener($eventListener, EventInterface $event, $name)
    {
        try {
            $this->callFunction($eventListener, $event);
            $status = self::EVENT_STATUS_OK;
        } catch (\Exception $e) {
            $this->addError($e);
            $status = self::EVENT_STATUS_ERROR;
        }

        $this->loggerInstance->makeLogEvent($name, $eventListener, $status);

        return $this;
    }

    /**
     * dynamically add new listener or listeners for given event name
     * listeners are added at end of the list
     *
     * @param string $eventName
     * @param array $listeners
     * @return $this
     */
    public function addEventListener($eventName, array $listeners)
    {
        if (!array_key_exists($eventName, $this->options['events'])) {
            $this->options['events'][$eventName] = [
                'object' =>BaseEvent::class,
                'listeners' => $listeners,
            ];
        }

        $this->options['events'][$eventName]['listeners'] = array_merge(
            $this->options['events'][$eventName]['listeners'],
            $listeners
        );

        return $this;
    }

    /**
     * allow to call event listeners functions
     *
     * @param mixed $listener
     * @param EventInterface $event
     */
    protected function callFunction($listener, EventInterface $event)
    {
        if (is_callable($listener)) {
            call_user_func($listener, $event);
        }
    }

    /**
     * read configuration from file
     *
     * @param mixed $path
     * @param string $type
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function readEventConfiguration($path, $type)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException('File ' . $path . 'don\'t exists.');
        }

        $name = '\BlueEvent\Event\Config\\' . ucfirst($type) . 'Config';

        if (!class_exists($name)) {
            throw new \InvalidArgumentException('Incorrect configuration type: ' . $type);
        }

        /** @var \BlueEvent\Event\Config\ConfigReader $reader */
        $reader = new $name;

        return $this->setEventConfiguration($reader->readConfig($path));
    }

    /**
     * disable or enable event logging (true to enable)
     *
     * @param bool $logEvents
     * @return $this
     */
    public function setEventLog($logEvents)
    {
        $this->options['log_events'] = (bool)$logEvents;
        return $this;
    }

    /**
     * log given events by given name
     *
     * @param array $events
     * @return $this
     */
    public function logEvent(array $events = [])
    {
        foreach ($events as $event) {
            if (!in_array($event, $this->loggerInstance->logEvents, true)) {
                $this->loggerInstance->logEvents[] = $event;
            }
        }

        return $this;
    }

    /**
     * enable or disable log all events
     *
     * @param bool $log
     * @return $this
     */
    public function logAllEvents($log = true)
    {
        $this->options['log_all_events'] = (bool)$log;
        return $this;
    }

    /**
     * get complete object configuration or value of single option
     *
     * @param $option string|null
     * @return mixed
     */
    public function getConfiguration($option = null)
    {
        if (!is_null($option)) {
            return $this->options[$option];
        }

        return $this->options;
    }

    /**
     * return list of all events to log
     *
     * @return array
     */
    public function getAllEventsToLog()
    {
        return $this->loggerInstance->logEvents;
    }

    /**
     * return current event configuration
     *
     * @return array
     */
    public function getEventConfiguration()
    {
        return $this->options['events'];
    }

    /**
     * return all event dispatcher errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errorList;
    }

    /**
     * return information that event dispatcher has some errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->hasErrors;
    }

    /**
     * clear all event dispatcher errors
     *
     * @return $this
     */
    public function clearErrors()
    {
        $this->errorList = [];
        $this->hasErrors = false;

        return $this;
    }

    /**
     * add new error to list
     *
     * @param \Exception $exception
     * @return $this
     */
    protected function addError(\Exception $exception)
    {
        $this->errorList[$exception->getCode()] = [
            'message'   => $exception->getMessage(),
            'line'      => $exception->getLine(),
            'file'      => $exception->getFile(),
            'trace'     => $exception->getTraceAsString(),
        ];
        $this->hasErrors = true;

        return $this;
    }
}
