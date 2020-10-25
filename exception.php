<?php

/*
 * Very simplistic version of PEAR.php
 */

class Exception
{
    public $message = '';

    public function isException($data)
    {
        return (bool)(is_object($data)
                      && ('exception' == get_class($data)));
    }

    public function __construct($message = 'unknown error')
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return ($this->message);
    }
}
