<?php

namespace Graze\DataFlow\Flow\File\Collection;

use Closure;
use Exception;
use Graze\DataFlow\Flow\File\Exception\DirectoryDoesNotExistException;
use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeCollectionInterface;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;

class AddFilesFromDirectory extends Flow implements ExtensionInterface
{
    /**
     * @param ExtensibleInterface $extensible
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $extensible, $method)
    {
        return (
            ($extensible instanceof FileNodeCollectionInterface) &&
            ($method == 'addFilesFromDirectory')
        );
    }

    /**
     * Add files from a local Directory
     *
     * @extend Graze\DataFlow\Node\File\FileNodeCollectionInterface
     * @param FileNodeCollectionInterface $collection
     * @param string                      $directory
     * @param Closure                     $fileCreator ($path) -> FileNodeInterface
     * @param bool                        $recursive
     * @return FileNodeCollectionInterface
     * @throws DirectoryDoesNotExistException
     */
    public function addFilesFromDirectory(
        FileNodeCollectionInterface $collection,
        $directory,
        Closure $fileCreator,
        $recursive = false
    ) {
        $directory = $this->addTrailingSlash($directory);
        try {
            $dir = dir($directory);
        } catch (Exception $e) {
            throw new DirectoryDoesNotExistException($directory);
        }
        while ($file = $dir->read()) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            if (is_dir($directory . $file)) {
                if ($recursive) {
                    $this->addFilesFromDirectory($collection, $directory . $file, $fileCreator, $recursive);
                }
            } else {
                $createdFile = call_user_func($fileCreator, $directory . $file);
                $collection->add($createdFile);
            }
        }
        return $collection;
    }

    /**
     * @param string $directory
     * @return string
     */
    private function addTrailingSlash($directory)
    {
        return (substr($directory, -1) !== '/') ?
            $directory . '/' :
            $directory;
    }
}
