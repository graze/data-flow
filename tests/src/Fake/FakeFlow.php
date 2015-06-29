<?php

namespace Graze\DataFlow\Test\Fake;

use Graze\DataFlow\FlowableInterface;
use Graze\DataFlow\FlowInterface;

class FakeFlow implements FlowInterface
{
    static public $canFlow = true;

    /**
     * Determine if this object can act upon the supplied node
     *
     * @param FlowableInterface $node
     * @return bool
     */
    public function canFlow(FlowableInterface $node)
    {
        return static::$canFlow;
    }

    /**
     * Variadic function to run the flow.
     *
     * @param FlowableInterface $node
     * @param array             $arguments
     * @return mixed
     */
    public function flow(FlowableInterface $node, $arguments)
    {
        return true;
    }
}
