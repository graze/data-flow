<?php

namespace Graze\DataFlow\Test\Unit\Flow;

use Graze\DataFlow\Flow\FlowCollection;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use IteratorAggregate;
use Mockery as m;
use Serializable;

class FlowCollectionTest extends TestCase
{
    public function testInstanceOf()
    {
        $collection = new FlowCollection();
        static::assertInstanceOf(Serializable::class, $collection);
        static::assertInstanceOf(IteratorAggregate::class, $collection);
    }

    public function testConstructWithMultipleFlows()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $collection = new FlowCollection($flow1, $flow2);

        static::assertEquals([$flow1, $flow2], $collection->getAll());
    }

    public function testAddWillAppendTheFlow()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $collection = new FlowCollection($flow1, $flow2);

        $flow3 = m::mock(FlowInterface::class);
        $collection->add($flow3);

        static::assertEquals([$flow1, $flow2, $flow3], $collection->getAll());
    }

    public function testAddFlowsWillAppendTheFlow()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $collection = new FlowCollection($flow1, $flow2);

        $flow3 = m::mock(FlowInterface::class);
        $flow4 = m::mock(FlowInterface::class);

        $collection->addFlows([$flow3, $flow4]);

        static::assertEquals([$flow1, $flow2, $flow3, $flow4], $collection->getAll());
    }

    public function testCount()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $collection = new FlowCollection($flow1, $flow2);

        static::assertEquals(2, $collection->count());
    }

    public function testRemove()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $collection = new FlowCollection($flow1, $flow2);

        static::assertEquals(2, $collection->count());

        $collection->remove($flow2);

        static::assertEquals(1, $collection->count());
    }

    public function testIterator()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $collection = new FlowCollection($flow1, $flow2);

        foreach ($collection->getIterator() as $flow) {
            static::assertContains($flow, [$flow1, $flow2]);
        }
    }

    public function testSerialize()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $collection = new FlowCollection($flow1, $flow2);

        $serialized = $collection->serialize();

        $newCollection = new FlowCollection();
        $newCollection->unserialize($serialized);

        static::assertEquals(2, $newCollection->count());
    }
}
