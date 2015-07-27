<?php

namespace Graze\DataFlow\Node;

interface DataNodeInterface
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * Return a clone of this object
     *
     * @return DataNodeInterface
     */
    public function getClone();
}
