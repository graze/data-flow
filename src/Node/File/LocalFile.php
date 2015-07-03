<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataFlow\Flow\File\Compression\CompressionType;
use Graze\DataFlow\Node\DataNode;

class LocalFile extends DataNode implements FileNodeInterface
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var mixed
     */
    private $contents;

    /**
     * @var string - CompressionType::
     */
    private $compression;

    /**
     * @param string $filePath
     * @param string $compression
     */
    public function __construct($filePath, $compression = CompressionType::NONE)
    {
        if (file_exists($filePath)) {
            $filePath = realpath($filePath);
        }

        $pathInfo = pathinfo($filePath);
        $this->filename = $pathInfo['basename'];
        $this->directory = $pathInfo['dirname'] . '/';
        $this->compression = $compression;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->directory . $this->filename;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return array
     */
    public function getContents()
    {
        if (!$this->exists()) {
            return [];
        }

        if (!$this->contents) {
            if ($this->compression != CompressionType::NONE) {
                $extractedFile = $this->decompress(['keepOldFile' => true]);
                $this->contents = $extractedFile->getContents();
                unlink($extractedFile);
            } else {
                $this->contents = file($this->getFilePath());
            }
        }

        return $this->contents;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFilePath();
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->getFilePath());
    }

    /**
     * @return string - see CompressionType::
     */
    public function getCompression()
    {
        return $this->compression;
    }
}
