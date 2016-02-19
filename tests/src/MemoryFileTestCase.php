<?php

namespace Graze\DataFlow\Test;

use Graze\DataFile\Node\FileNode;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

abstract class MemoryFileTestCase extends TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    protected function setUp()
    {
        $this->filesystem = new Filesystem(new MemoryAdapter());
    }

    /**
     * @param string      $path
     * @param string|null $contents
     *
     * @return FileNode
     */
    protected function makeFile($path, $contents = null)
    {
        $file = new FileNode($this->filesystem, $path);
        if ($contents) {
            $file->write($contents);
        }
        return $file;
    }
}
