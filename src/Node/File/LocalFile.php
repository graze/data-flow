<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use Graze\DataFlow\Node\DataNode;

class LocalFile extends DataNode implements FileNodeInterface
{
    /**
     * @var string - CompressionType::
     */
    protected $compression;

    /**
     * @var string|null
     */
    protected $encoding;

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
     * @param string $filePath
     * @param array  $options     -compression <string> (Optional) one of CompressionType::
     *                            -encoding <string> (Optional) An encoding string defined in iconv
     */
    public function __construct($filePath, $options = [])
    {
        if (file_exists($filePath)) {
            $filePath = realpath($filePath);
        }

        $pathInfo = pathinfo($filePath);
        $this->filename = $pathInfo['basename'];
        $this->directory = $pathInfo['dirname'] . '/';
        $this->compression = (isset($options['compression'])) ? $options['compression'] : CompressionType::NONE;
        $this->encoding = (isset($options['encoding'])) ? $options['encoding'] : null;
    }

    /**
     * @param $filePath
     * @return LocalFile
     */
    public function setFilePath($filePath)
    {
        $pathInfo = pathinfo($filePath);
        $this->directory = $pathInfo['dirname'] . '/';
        $this->filename = $pathInfo['basename'];

        return $this;
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
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->getFilePath());
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
    public function __toString()
    {
        return $this->getFilePath();
    }

    /**
     * @return string - see CompressionType::
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * @param string $compression
     * @return LocalFile
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param null|string $encoding
     * @return LocalFile
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Return a clone of this object
     *
     * @return LocalFile
     */
    public function getClone()
    {
        return clone $this;
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
