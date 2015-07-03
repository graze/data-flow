<?php

namespace Graze\DataFlow\Flow\File\Compression;

use Exception;

class InvalidCompressionTypeException extends Exception
{
    public function __construct($compression, $message = '', Exception $previous = null)
    {
        $message = "Unknown compression type: $compression. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
