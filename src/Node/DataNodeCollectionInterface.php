<?php

namespace Graze\DataFlow\Node;

use Graze\DataStructure\Collection\CollectionInterface;

interface DataNodeCollectionInterface extends CollectionInterface
{
    /**
     * @param callable $fn
     * @return DataNodeCollectionInterface
     */
    public function apply($fn);
}
