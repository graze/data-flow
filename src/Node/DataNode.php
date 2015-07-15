<?php

namespace Graze\DataFlow\Node;

use Graze\Extensible\AutoExtensible;

/**
 * Class DataNode
 *
 * A Node represents a block of data in a particular state (such as database table, file, etc)
 *
 * All setters on Nodes should be fluent (return $this) for call chaining
 *
 * @package Graze\DataFlow\Node
 */
class DataNode extends AutoExtensible implements DataNodeInterface
{

}
