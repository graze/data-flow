<?php

namespace Graze\DataFlow\Flowable\Finder;

use Graze\DataFlow\Flow\FlowInterface;
use Graze\DataFlow\Flowable\FlowableInterface;

/**
 * Interface FlowFinderInterface
 *
 * A Flow Finder attempts to resolve a given flow from a node and a command
 *
 * @package Graze\DataFlow
 */
interface FlowFinderInterface
{
    /**
     * @param FlowableInterface $flowable
     * @param string            $method
     * @return FlowInterface
     */
    public function get(FlowableInterface $flowable, $method);
}
