<?php

namespace SimpleLog\Message;

class DefaultJsonMessage extends DefaultMessage
{
    /**
     * @var array
     */
    protected $messageScheme = [];

    /**
     * @var array
     */
    protected $context = [];

    /**
     * @param string|array|object $message
     * @param array $context
     * @return $this
     */
    public function createMessage($message, array $context)
    {
        $this->context = $context;

        list($date, $time) = explode(';', strftime(self::DATE_FORMAT . ';' . self::TIME_FORMAT, time()));

        $this->messageScheme['date'] = $date;
        $this->messageScheme['time'] = $time;

        if (method_exists($message, '__toString')) {
            $message = (string)$message;
        }

        $this->messageScheme['data'] = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $this->message = json_encode($this->messageScheme);
        $this->buildContext($this->context);

        return $this->message;
    }
}
