<?php

namespace Graze\DataFlow\Flow;

use Graze\DataFlow\Flowable\FlowableInterface;

/**
 * Interface FlowInterface
 *
 * The Flow Interface is for doing stuff in a flow. These take action on the Flowable objects.
 *
 * @package Graze\DataFlow
 */
interface FlowInterface
{
    /**
     * Determine if this object can act upon the supplied node
     *
     * @param FlowableInterface $node
     * @param string            $method
     * @return bool
     */
    public function canFlow(FlowableInterface $node, $method);
}
