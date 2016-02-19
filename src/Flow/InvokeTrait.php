<?php

namespace Graze\DataFlow\Flow;

use Graze\DataNode\NodeInterface;

trait InvokeTrait
{
    /**
     * Invoke this Flow
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function __invoke(NodeInterface $node)
    {
        return $this->flow($node);
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    abstract public function flow(NodeInterface $node);
}
