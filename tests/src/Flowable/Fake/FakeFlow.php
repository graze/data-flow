<?php

namespace Graze\DataFlow\Test\Flowable\Fake;

use Graze\DataFlow\Flow\FlowInterface;
use Graze\DataFlow\Flowable\FlowableInterface;

class FakeFlow implements FlowInterface
{
    static public $canFlow = true;

    /**
     * Determine if this object can act upon the supplied node
     *
     * @param FlowableInterface $node
     * @param                   $method
     * @return bool
     */
    public function canFlow(FlowableInterface $node, $method)
    {
        return static::$canFlow;
    }

    /**
     * @param FlowableInterface $node
     * @return bool
     */
    public function fake(FlowableInterface $node)
    {
        return true;
    }
}
