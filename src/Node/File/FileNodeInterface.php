<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataFlow\Node\DataNodeInterface;

interface FileNodeInterface extends DataNodeInterface
{
    /**
     * @return mixed
     */
    public function getDirectory();

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
     * @return bool
     */
    public function exists();
}
