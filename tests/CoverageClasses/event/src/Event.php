<?php
/**
 * Event Object Class
 *
 * @package     BlueEvent
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 */

namespace BlueEvent\Event\Base;

use BlueEvent\Event\Base\Interfaces\EventInterface;

abstract class Event implements EventInterface
{
    /**
     * store information how many times event object was called
     *
     * @var int
     */
    protected static $launchCount = 0;

    /**
     * store information that event propagation is stopped or not
     *
     * @var bool
     */
    protected $propagationStopped = false;

    /**
     * @var array
     */
    protected $eventParameters = [];

    /**
     * @var string
     */
    protected $eventName = '';

    /**
     * create event instance
     *
     * @param string $eventName
     * @param array $parameters
     */
    public function __construct($eventName, array $parameters)
    {
        $this->eventName = $eventName;
        $this->eventParameters = $parameters;

        self::$launchCount++;
    }

    /**
     * return number how many times event was called
     *
     * @return int
     */
    public static function getLaunchCount()
    {
        return self::$launchCount;
    }

    /**
     * return information that event propagation is stopped or not
     *
     * @return bool
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * allow to stop event propagation
     *
     * @return $this
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventCode()
    {
        return $this->eventName;
    }

    /**
     * @return array
     */
    public function getEventParameters()
    {
        return $this->eventParameters;
    }
}
