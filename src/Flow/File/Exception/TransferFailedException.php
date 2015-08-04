<?php

namespace Graze\DataFlow\Flow\File\Exception;

use Exception;
use Graze\DataFlow\Node\File\FileNode;

class TransferFailedException extends Exception
{
    /**
     * MakeDirectoryFailedException constructor.
     *
     * @param FileNode  $from
     * @param FileNode  $to
     * @param string    $message
     * @param Exception $previous
     */
    public function __construct(FileNode $from, FileNode $to, $message = '', Exception $previous = null)
    {
        $message = "Failed to transfer file: $from to $to. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
