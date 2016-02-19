<?php

namespace Graze\DataFlow\Flow\Collection;

use Graze\DataFlow\Flow\AbstractFlow;
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

/**
 * Apply a callback to each element of a NodeCollection
 */
class Map extends AbstractFlow
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * First constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeCollectionInterface
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof NodeCollectionInterface)) {
            throw new InvalidArgumentException("The supplied $node is not a NodeCollectionInterface");
        }

        $class = get_class($node);

        return new $class($node->map($this->callback));
    }
}
