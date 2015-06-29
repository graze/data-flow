<?php

namespace Graze\DataFlow\Test\Fake;

use Graze\DataFlow\Extension\ExtensionAbstract;
use Graze\DataFlow\Extension\FlowExtension;
use Graze\DataFlow\Finder\FlowFinderInterface;
use Graze\DataFlow\Flowable;
use Graze\DataFlow\FlowableInterface;

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
