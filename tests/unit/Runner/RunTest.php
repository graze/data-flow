<?php

namespace Graze\DataFlow\Test\Unit\Runner;

use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\FlowCollection;
use Graze\DataFlow\Flow\Runner\Run;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeInterface;
use Mockery as m;

class RunTest extends TestCase
{
    public function testInstanceOf()
    {
        $runner = new Run();
        static::assertInstanceOf(FlowCollection::class, $runner);
        static::assertInstanceOf(FlowInterface::class, $runner);
    }

    public function testRunSequentially()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $runner = new Run($flow1, $flow2);

        $node1 = m::mock(NodeInterface::class);
        $node2 = m::mock(NodeInterface::class);
        $node3 = m::mock(NodeInterface::class);

        $flow1->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node2);
        $flow2->shouldReceive('flow')
              ->with($node2)
              ->andReturn($node3);

        static::assertEquals($node3, $runner->flow($node1));
    }

    public function testStaticInstance()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $runner = Flow::run($flow1, $flow2);

        $node1 = m::mock(NodeInterface::class);
        $node2 = m::mock(NodeInterface::class);
        $node3 = m::mock(NodeInterface::class);

        $flow1->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node2);
        $flow2->shouldReceive('flow')
              ->with($node2)
              ->andReturn($node3);

        static::assertEquals($node3, $runner->flow($node1));
    }

    public function testInvokeFlow()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $runner = Flow::run($flow1, $flow2);

        $node1 = m::mock(NodeInterface::class);
        $node2 = m::mock(NodeInterface::class);
        $node3 = m::mock(NodeInterface::class);

        $flow1->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node2);
        $flow2->shouldReceive('flow')
              ->with($node2)
              ->andReturn($node3);

        static::assertEquals($node3, call_user_func($runner, $node1));
    }
}
