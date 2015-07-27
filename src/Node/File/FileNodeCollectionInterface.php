<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataStructure\Collection\CollectionInterface;
use Graze\Extensible\ExtensibleInterface;

/**
 * Interface FileNodeCollectionInterface
 *
 * A Collection of FileNodeInterface
 *
 * @package Graze\DataFlow\Node\File
 */
interface FileNodeCollectionInterface extends CollectionInterface, ExtensibleInterface
{
    /**
     * For a given set of files, return any common prefix (i.e. directory, s3 key)
     *
     * @return string|null
     */
    public function getCommonPrefix();
}
