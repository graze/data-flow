<?php

namespace Graze\DataFlow\Flow\File\Exception;

use Exception;

class DirectoryDoesNotExistException extends Exception
{
    /**
     * DirectoryDoesNotExistException constructor.
     *
     * @param string    $directory
     * @param string    $message
     * @param Exception $previous
     */
    public function __construct($directory, $message = '', Exception $previous = null)
    {
        $message = "The directory: '$directory' does not exist. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
