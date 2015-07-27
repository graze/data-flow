<?php

namespace Graze\DataFlow\Flow\File\Expand;

use Graze\DataFlow\Node\File\FileNodeCollectionInterface;
use Graze\DataFlow\Node\File\FileNodeInterface;

interface FileExpanderInterface
{
    /**
     * @param FileNodeInterface $file
     * @return bool
     */
    public function canExpand(FileNodeInterface $file);

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeInterface $file
     * @param array             $options
     * @return FileNodeCollectionInterface
     */
    public function expand(FileNodeInterface $file, array $options = []);
}
