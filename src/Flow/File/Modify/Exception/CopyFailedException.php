<?php

namespace Graze\DataFlow\Flow\File\Modify\Exception;

use Exception;
use Graze\DataFlow\Node\File\FileNodeInterface;

class CopyFailedException extends Exception
{
    public function __construct(FileNodeInterface $fromFile, FileNodeInterface $toFile, $message = '', Exception $previous = null)
    {
        $message = "Failed to copy file from: '$fromFile' to '$toFile'. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
