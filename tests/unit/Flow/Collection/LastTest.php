<?php

namespace Graze\DataFlow\Test\Unit\Flow\Collection;

use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\Collection\Last;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollectionInterface;
use Mockery as m;

class LastTest extends TestCase
{
    public function testInstanceOf()
    {
        $flow = new Last(function ($node) {
            return true;
        });

        static::assertInstanceOf(FlowInterface::class, $flow);
    }

    public function testFlow()
    {
        $func = function ($node) use (&$called) {
            $called = true;
            return true;
        };
        $flow = new Last($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('last')
             ->with($func)
             ->andReturn($node);

        $response = $flow->flow($node);

        static::assertSame($response, $node);
    }

    public function testStaticFlow()
    {
        $func = function ($node) use (&$called) {
            $called = true;
            return true;
        };
        $flow = Flow::last($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('last')
             ->with($func)
             ->andReturn($node);

        $response = $flow->flow($node);

        static::assertSame($response, $node);
    }

    public function testInvokeFlow()
    {
        $func = function ($node) use (&$called) {
            $called = true;
            return true;
        };
        $flow = Flow::last($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('last')
             ->with($func)
             ->andReturn($node);

        $response = call_user_func($flow, $node);

        static::assertSame($response, $node);
    }
}
