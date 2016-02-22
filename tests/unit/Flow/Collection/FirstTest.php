<?php

namespace Graze\DataFlow\Test\Unit\Flow\Collection;

use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\Collection\First;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class FirstTest extends TestCase
{
    public function testInstanceOf()
    {
        $flow = new First(function ($node) {
            return true;
        });

        static::assertInstanceOf(FlowInterface::class, $flow);
    }

    public function testInvalidInputThrowsAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new First(function () {
        });

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testFlow()
    {
        $func = function ($node) use (&$called) {
            $called = true;
            return true;
        };
        $flow = new First($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('first')
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
        $flow = Flow::first($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('first')
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
        $flow = Flow::first($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('first')
             ->with($func)
             ->andReturn($node);

        $response = call_user_func($flow, $node);

        static::assertSame($response, $node);
    }
}
