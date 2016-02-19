<?php

namespace Graze\DataFlow\Test\Unit\Flow\Collection;

use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\Collection\Each;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use Mockery as m;

class EachTest extends TestCase
{
    public function testInstanceOf()
    {
        $flow = new Each(Flow::callback(function ($node) {
            return $node;
        }));

        static::assertInstanceOf(FlowInterface::class, $flow);
    }

    public function testFlow()
    {
        $eachFlow = m::mock(FlowInterface::class);
        $flow = new Each($eachFlow);

        $node = m::mock(NodeInterface::class);
        $collection = new NodeCollection([$node]);

        $eachFlow->shouldReceive('flow')
                 ->with($node)
                 ->andReturn($node);

        $response = $flow->flow($collection);

        static::assertNotSame($response, $collection);
        static::assertEquals($collection->getAll(), $response->getAll());
        static::assertInstanceOf(NodeCollectionInterface::class, $response);
    }

    public function testStaticFlow()
    {
        $eachFlow = m::mock(FlowInterface::class);
        $flow = Flow::each($eachFlow);

        $node = m::mock(NodeInterface::class);
        $collection = new NodeCollection([$node]);

        $eachFlow->shouldReceive('flow')
                 ->with($node)
                 ->andReturn($node);

        $response = $flow->flow($collection);

        static::assertNotSame($response, $collection);
        static::assertEquals($collection->getAll(), $response->getAll());
        static::assertInstanceOf(NodeCollectionInterface::class, $response);
    }

    public function testInvokeFlow()
    {
        $eachFlow = m::mock(FlowInterface::class);
        $flow = new Each($eachFlow);

        $node = m::mock(NodeInterface::class);
        $collection = new NodeCollection([$node]);

        $eachFlow->shouldReceive('flow')
                 ->with($node)
                 ->andReturn($node);

        $response = call_user_func($flow, $collection);

        static::assertNotSame($response, $collection);
        static::assertEquals($collection->getAll(), $response->getAll());
        static::assertInstanceOf(NodeCollectionInterface::class, $response);
    }
}
