<?php

namespace Graze\DataFlow\Flowable;

use Graze\DataFlow\Flowable\Finder\ContainerFlowFinder;
use Graze\DataFlow\Flowable\Finder\FlowFinderInterface;

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
