<?php

namespace Graze\DataFlow\Flow\File;

use Graze\DataFlow\Flow\File\Exception\MakeDirectoryFailedException;
use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;

class MakeDirectory extends Flow implements ExtensionInterface
{
    /**
     * Create the directory specified by the $file if it does not exist
     *
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile $file
     * @param int       $mode Octal Mode
     * @return LocalFile The original file inputted
     * @throws MakeDirectoryFailedException
     */
    public function makeDirectory(LocalFile $file, $mode = 0777)
    {
        if (!file_exists($file->getDirectory())) {
            if (!@mkdir($file->getDirectory(), $mode, true)) {
                $lastError = error_get_last();
                throw new MakeDirectoryFailedException($file, $lastError['message']);
            }
        }

        return $file;
    }

    /**
     * @param ExtensibleInterface $extensible
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $extensible, $method)
    {
        return (($extensible instanceof LocalFile) &&
            ($method == 'makeDirectory'));
    }
}
