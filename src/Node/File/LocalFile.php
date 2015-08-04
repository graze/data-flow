<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class LocalFile extends FileNode implements LocalFileNodeInterface
{
    const LOCAL_PREFIX = 'local';

    /**
     * @var string - CompressionType::
     */
    protected $compression;

    /**
     * @var string|null
     */
    protected $encoding;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct(new FileSystem(new Local('/')), $path);

        $this->compression = CompressionType::NONE;
        $this->encoding = null;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if ($this->exists() &&
            $this->getCompression() != CompressionType::NONE
        ) {
            $uncompressed = $this->decompress();
            $content = $uncompressed->getContents();
            $uncompressed->delete();
            return $content;
        } else {
            return parent::getContents();
        }
    }

    /**
     * @return string - see CompressionType::
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * @param string $compression - @see CompressionType::
     * @return $this
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;

        return $this;
    }
}
