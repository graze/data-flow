<?php

namespace Graze\DataFlow\Node\File;

interface LocalFileNodeInterface
{
    /**
     * @return string - see CompressionType::
     */
    public function getCompression();

    /**
     * @return string
     */
    public function getEncoding();

    /**
     * @param string $compression - @see CompressionType::
     * @return $this
     */
    public function setCompression($compression);

    /**
     * @param string $encoding
     * @return $this
     */
    public function setEncoding($encoding);
}
