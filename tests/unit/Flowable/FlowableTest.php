<?php

namespace Graze\DataFlow\Test\Unit\Flowable;

use Graze\DataFlow\Test\Flowable\Fake\FakeFlowable;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

class FlowableTest extends TestCase
{
    public function testFinderIsContainerFinder()
    {
        $flowable = new FakeFlowable();

        static::assertInstanceOf('Graze\DataFlow\Flowable\Flowable', $flowable, 'FakeFlowable does not implement Flowable abstract');

        $finder = $flowable->getFinder();

        static::assertInstanceOf('Graze\DataFlow\Flowable\Finder\ContainerFlowFinder', $finder);
    }
}
