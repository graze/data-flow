<?php

namespace Graze\DataFlow\Node\File\Source;

use Graze\DataFlow\Node\File\FileNodeCollectionInterface;

interface FileSourceInterface
{
    /**
     * @return FileNodeCollectionInterface
     */
    public function getFiles();
}
