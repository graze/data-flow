<?php

namespace Graze\DataFlow\Node\File;

interface FileNodeInterface
{
    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @return string
     */
    public function getFilename();

    /**
     * Returns the contents of the file as an array.
     *
     * @return array
     */
    public function getContents();

    /**
     * @return string - see CompressionType::
     */
    public function getCompression();

    /**
     * @return string
     */
    public function getEncoding();

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return bool
     */
    public function exists();
}
