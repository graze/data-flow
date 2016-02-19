<?php

namespace Graze\DataFlow\Flow;

use Graze\DataNode\NodeInterface;
use Psr\Log\LogLevel;

class Callback extends AbstractFlow
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Callback constructor.
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
        $this->log(LogLevel::DEBUG, "Running callback flow");
        return call_user_func($this->callback, $node);
    }
}
