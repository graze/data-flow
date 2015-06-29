<?php

namespace Graze\DataFlow\Test;

use Graze\DataFlow\Test\Fake\FakeFlowable;
use Mockery as m;

class FlowableTest extends TestCase
{
    public function testFinderIsContainerFinder()
    {
        $flowable = new FakeFlowable();

        static::assertInstanceOf('Graze\DataFlow\Flowable', $flowable, 'FakeFlowable does not implement Flowable abstract');

        $finder = $flowable->getFinder();

        static::assertInstanceOf('Graze\DataFlow\Finder\ContainerFlowFinder', $finder);
    }
}
