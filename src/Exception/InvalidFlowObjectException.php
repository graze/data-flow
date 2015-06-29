<?php

namespace Graze\DataFlow\Exception;

use Exception;

class InvalidFlowObjectException extends Exception
{
    public function __construct($className, $message = '', Exception $previous = null)
    {
        $message = "The flow class specified in: $className does not implement FlowInterface. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
