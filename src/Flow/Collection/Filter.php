<?php

namespace Graze\DataFlow\Flow\Collection;

use Graze\DataFlow\Flow\AbstractFlow;
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

/**
 * Return only the entries in a NodeCollection that match the Filter callback provided
 */
class Filter extends AbstractFlow
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
     * @inheritDoc
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof NodeCollectionInterface)) {
            throw new InvalidArgumentException("The supplied $node is not a NodeCollectionInterface");
        }

        return $node->filter($this->callback);
    }
}
