<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataFlow\Container\ContainerExtensible;
use Graze\DataFlow\Flow\File\Modify\Exception\CopyFailedException;
use Graze\DataFlow\Format\FormatAwareInterface;
use Graze\DataFlow\Format\FormatAwareTrait;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\Finder\FinderAwareInterface;
use League\Flysystem\File;

class FileNode extends File implements FileNodeInterface, ExtensibleInterface, FinderAwareInterface, FormatAwareInterface
{
    use ContainerExtensible;
    use FormatAwareTrait;


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getPath();
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        return pathinfo($this->path, PATHINFO_DIRNAME) . '/';
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    /**
     * Returns the contents of the file as an array.
     *
     * @return array
     */
    public function getContents()
    {
        if ($this->exists()) {
            return explode("\n", trim($this->read()));
        } else {
            return [];
        }
    }

    /**
     * @param string|null $newpath
     * @return false|File
     * @throws CopyFailedException When it is unable to copy the file
     */
    public function copy($newpath = null)
    {
        if (!$newpath) {
            $newpath = $this->path . '-copy';
        }
        if (@$this->filesystem->copy($this->path, $newpath)) {
            return $this->getClone()->setPath($newpath);
        } else {
            $lastError = error_get_last();
            throw new CopyFailedException($this, $newpath, $lastError['message']);
        }
    }

    /**
     * Return a clone of this object
     *
     * @return self
     */
    public function getClone()
    {
        return clone $this;
    }

    /**
     * Clone sub objects
     */
    public function __clone()
    {
        if ($this->format) {
            $this->format = clone $this->format;
        }
    }
}
