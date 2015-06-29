<?php

namespace Graze\DataFlow\Exception;

use Exception;

class InvalidFlowCommandException extends Exception
{
    public function __construct($command, $flowableClass, $message = '', Exception $previous = null)
    {
        $message = "The command: $command cannot be applied to $flowableClass. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
