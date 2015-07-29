<?php

namespace Graze\DataFlow\Flow\File\Contract;

use Graze\DataFlow\Node\File\FileNodeCollectionInterface;
use Graze\DataFlow\Node\File\FileNodeInterface;

interface FileContractorInterface
{
    /**
     * @param FileNodeCollectionInterface $files
     * @return bool
     */
    public function canContract(FileNodeCollectionInterface $files);

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeCollectionInterface $files
     * @param array                       $options
     * @return FileNodeInterface
     */
    public function contract(FileNodeCollectionInterface $files, array $options = []);
}
