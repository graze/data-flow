<?php

namespace Graze\DataFlow\Node;

use Graze\DataFlow\Container\ContainerExtensible;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\Finder\FinderAwareInterface;
use League\Container\ContainerAwareInterface;
use phpDocumentor\Reflection\DocBlock;

/**
 * Class DataNode
 *
 * A Node represents a block of data in a particular state (such as database table, file, etc)
 *
 * All setters on Nodes should be fluent (return $this) for call chaining
 *
 * @package Graze\DataFlow\Node
 */
abstract class DataNode implements DataNodeInterface, ContainerAwareInterface, ExtensibleInterface, FinderAwareInterface
{
    use ContainerExtensible;
}
