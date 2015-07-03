<?php

namespace Graze\DataFlow\Flowable\Exception;

use Exception;

class InvalidFlowableObjectException extends Exception
{
    public function __construct($className, $message = '', Exception $previous = null)
    {
        $message = "The object: $className does not implement FlowableInterface. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
