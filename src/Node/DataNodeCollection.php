<?php

namespace Graze\DataFlow\Node;

use Graze\DataFlow\Container\ContainerExtensible;
use Graze\DataStructure\Collection\Collection;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\Finder\FinderAwareInterface;
use InvalidArgumentException;
use League\Container\ContainerAwareInterface;

/**
 * Class DataNodeCollection
 *
 * A Collection of DataNodes that can be acted upon by a flow
 *
 * @package Graze\DataFlow\Node
 */
class DataNodeCollection extends Collection implements DataNodeCollectionInterface, ExtensibleInterface, ContainerAwareInterface, FinderAwareInterface
{
    use ContainerExtensible;

    /**
     * {@inheritdoc}
     */
    public function add($value)
    {
        if (!($value instanceof DataNodeInterface)) {
            throw new InvalidArgumentException("The specified value does not implement DataNodeInterface");
        }
        return parent::add($value);
    }

    /**
     * {@inheritdoc}
     */
    public function apply($fn)
    {
        foreach ($this->getIterator() as &$item) {
            $out = call_user_func($fn, $item);
            if (isset($out) && ($out instanceof DataNodeInterface)) {
                $item = $out;
            }
        }

        return $this;
    }
}
