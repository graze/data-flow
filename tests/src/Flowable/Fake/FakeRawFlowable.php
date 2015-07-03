<?php

namespace Graze\DataFlow\Test\Flowable\Fake;

use Graze\DataFlow\Flowable\Extension\FlowExtension;
use Graze\DataFlow\Flowable\Finder\FlowFinderInterface;
use Graze\DataFlow\Flowable\FlowableInterface;

class FakeRawFlowable implements FlowableInterface
{
    use FlowExtension;

    /**
     * @var FlowFinderInterface
     */
    protected $finder;

    public function setFinder(FlowFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return FlowFinderInterface
     */
    public function getFinder()
    {
        return $this->finder;
    }
}
