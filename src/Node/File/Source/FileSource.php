<?php

namespace Graze\DataFlow\Node\File\Source;

use Graze\DataFlow\Node\File\FileNode;
use Graze\DataFlow\Node\File\FileNodeCollection;
use Graze\DataFlow\Node\File\FileNodeCollectionInterface;
use Graze\DataFlow\Utility\ArrayFilter\ArrayFilterInterface;
use League\Flysystem\FilesystemInterface;

/**
 * Class FileSource
 *
 * A Source of files defined by a FileSystem and searches within the filesystem for matching filter
 *
 * @package Graze\DataFlow\Node\File\Source
 */
class FileSource implements FileSourceInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var
     */
    private $directory;

    /**
     * @param FilesystemInterface  $filesystem
     * @param string               $directory
     * @param ArrayFilterInterface $filter Filter to handle metadata array @see FileSystemInterface::getMetadata
     */
    public function __construct(FilesystemInterface $filesystem, $directory, ArrayFilterInterface $filter)
    {
        $this->filesystem = $filesystem;
        $this->directory = $directory;
        $this->filter = $filter;
    }

    /**
     * @param bool $recursive
     * @return FileNodeCollectionInterface
     */
    public function getFiles($recursive = false)
    {
        $files = $this->filesystem->listContents($this->directory, $recursive);

        // only list files
        $onlyFiles = array_filter($files, function ($metadata) {
            return $metadata['type'] == 'file';
        });

        $matching = array_filter($onlyFiles, [$this->filter, 'matches']);

        $fileNodes = array_map(function ($metadata) {
            return new FileNode($this->filesystem, $metadata['path']);
        }, $matching);

        return new FileNodeCollection($fileNodes);
    }
}
