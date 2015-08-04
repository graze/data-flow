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
    public function getPath();

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
     * @return bool
     */
    public function exists();
}
