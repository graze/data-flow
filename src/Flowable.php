<?php

namespace Graze\DataFlow;

use Graze\DataFlow\Finder\ContainerFlowFinder;
use Graze\DataFlow\Finder\FlowFinderInterface;

abstract class Flowable implements FlowableInterface
{
    /**
     * @return FlowFinderInterface
     */
    public function getFinder()
    {
        return ContainerFlowFinder::getInstance();
    }
}
