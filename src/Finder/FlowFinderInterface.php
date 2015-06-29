<?php

namespace Graze\DataFlow\Finder;

use Graze\DataFlow\FlowableInterface;
use Graze\DataFlow\FlowInterface;

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
     * @param string            $command
     * @return FlowInterface
     */
    public function findFlow(FlowableInterface $flowable, $command);
}
