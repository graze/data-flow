<?php

namespace Graze\DataFlow\Flow\Collection;

use Graze\DataFlow\Flow\AbstractFlow;
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Psr\Log\LogLevel;

/**
 * Return the first element in a NodeCollection based on an option callback
 */
class First extends AbstractFlow
{
    /**
     * @var callable|null
     */
    private $callback;

    /**
     * First constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback = null)
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
        $this->log(LogLevel::DEBUG, "Selecting the first entry from {node}", ['node' => $node]);

        return $node->first($this->callback);
    }
}
