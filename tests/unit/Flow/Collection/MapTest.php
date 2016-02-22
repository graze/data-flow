<?php

namespace Graze\DataFlow\Test\Unit\Flow\Collection;

use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\Collection\Map;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class MapTest extends TestCase
{
    public function testInstanceOf()
    {
        $flow = new Map(function ($node) {
            return true;
        });

        static::assertInstanceOf(FlowInterface::class, $flow);
    }

    public function testInvalidInputThrowsAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Map(function () {
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
        $flow = new Map($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('map')
             ->with($func)
             ->andReturn([]);

        $response = $flow->flow($node);

        static::assertNotSame($response, $node);
        static::assertInstanceOf(NodeCollectionInterface::class, $response);
    }

    public function testStaticFlow()
    {
        $func = function ($node) use (&$called) {
            $called = true;
            return true;
        };
        $flow = Flow::map($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('map')
             ->with($func)
             ->andReturn([]);

        $response = $flow->flow($node);

        static::assertNotSame($response, $node);
        static::assertInstanceOf(NodeCollectionInterface::class, $response);
    }

    public function testInvokeFlow()
    {
        $func = function ($node) use (&$called) {
            $called = true;
            return true;
        };
        $flow = Flow::map($func);

        $node = m::mock(NodeCollectionInterface::class);
        $node->shouldReceive('map')
             ->with($func)
             ->andReturn([]);

        $response = call_user_func($flow, $node);

        static::assertNotSame($response, $node);
        static::assertInstanceOf(NodeCollectionInterface::class, $response);
    }
}
