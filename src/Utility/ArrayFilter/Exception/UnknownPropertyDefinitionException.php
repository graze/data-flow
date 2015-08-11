<?php

namespace Graze\DataFlow\Utility\ArrayFilter\Exception;

use Exception;

class UnknownPropertyDefinitionException extends Exception
{
    public function __construct($property, $message = '', Exception $e = null)
    {
        $message = "Unknown property definition: $property. " . $message;

        parent::__construct($message, 0, $e);
    }
}
