<?php

namespace Graze\DataFlow\Flow\Runner;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFlow\Flow\FlowCollection;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

class Run extends FlowCollection implements FlowInterface, LoggerAwareInterface
{
    use InvokeTrait;
    use OptionalLoggerTrait;

    /**
     * @inheritdoc
     */
    public function flow(NodeInterface $node)
    {
        $this->log(LogLevel::NOTICE, "Running through {count} flows", ['count' => count($this->items)]);

        $current = $node;
        foreach ($this->items as $flow) {
            $current = $flow->flow($current);
        }
        return $current;
    }
}
