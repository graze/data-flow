<?php

namespace Graze\DataFlow\Test\Unit\Runner;

use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\FlowCollection;
use Graze\DataFlow\Flow\Runner\ToAll;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeInterface;
use Mockery as m;

class ToAllTest extends TestCase
{
    public function testInstanceOf()
    {
        $runner = new ToAll();
        static::assertInstanceOf(FlowCollection::class, $runner);
        static::assertInstanceOf(FlowInterface::class, $runner);
    }

    public function testRunIndependent()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $runner = new ToAll($flow1, $flow2);

        $node1 = m::mock(NodeInterface::class);
        $node2 = m::mock(NodeInterface::class);
        $node3 = m::mock(NodeInterface::class);

        $flow1->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node2);
        $flow2->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node3);

        $output = $runner->flow($node1);

        static::assertInstanceOf(NodeCollection::class, $output);

        static::assertEquals([$node2, $node3], $output->getAll());
    }

    public function testStaticInstance()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $runner = Flow::toAll($flow1, $flow2);

        $node1 = m::mock(NodeInterface::class);
        $node2 = m::mock(NodeInterface::class);
        $node3 = m::mock(NodeInterface::class);

        $flow1->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node2);
        $flow2->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node3);

        $output = $runner->flow($node1);

        static::assertInstanceOf(NodeCollection::class, $output);

        static::assertEquals([$node2, $node3], $output->getAll());
    }

    public function testInvokeFlow()
    {
        $flow1 = m::mock(FlowInterface::class);
        $flow2 = m::mock(FlowInterface::class);

        $runner = Flow::toAll($flow1, $flow2);

        $node1 = m::mock(NodeInterface::class);
        $node2 = m::mock(NodeInterface::class);
        $node3 = m::mock(NodeInterface::class);

        $flow1->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node2);
        $flow2->shouldReceive('flow')
              ->with($node1)
              ->andReturn($node3);

        $output = call_user_func($runner, $node1);

        static::assertInstanceOf(NodeCollection::class, $output);

        static::assertEquals([$node2, $node3], $output->getAll());
    }
}
