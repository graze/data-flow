<?php

namespace Graze\DataFlow\Flow\Runner;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFlow\Flow\FlowCollection;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Runs through a set of flows but providing the initial node to all flows and return th
 */
class ToAll extends FlowCollection implements FlowInterface, LoggerAwareInterface
{
    use InvokeTrait;
    use OptionalLoggerTrait;

    /**
     * @inheritdoc
     */
    public function flow(NodeInterface $node)
    {
        $collection = new NodeCollection();
        foreach ($this->items as $flow) {
            $collection->add($flow->flow($node));
        }
        return $collection;
    }
}
