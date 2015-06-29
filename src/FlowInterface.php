<?php

namespace Graze\DataFlow;

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
     * @return bool
     */
    public function canFlow(FlowableInterface $node);

    /**
     * Variadic function to run the flow.
     *
     * @param FlowableInterface $node
     * @param array $arguments
     * @return mixed
     */
    public function flow(FlowableInterface $node, $arguments);
}
