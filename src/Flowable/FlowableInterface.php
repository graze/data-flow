<?php

namespace Graze\DataFlow\Flowable;

use Graze\DataFlow\Flowable\Finder\FlowFinderInterface;

/**
 * Interface FlowableInterface
 *
 * An object that implements the FlowableInterface is something that can be acted upon by the flow processors.
 *
 * @package Graze\DataFlow
 */
interface FlowableInterface
{
    /**
     * @return FlowFinderInterface
     */
    public function getFinder();
}
