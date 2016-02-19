<?php

namespace Graze\DataFlow;

use Graze\DataNode\NodeInterface;

interface FlowInterface
{
    /**
     * Run a 'flow' through the handler
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function flow(NodeInterface $node);
}
