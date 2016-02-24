<?php
/**
 * This file is part of graze/data-flow
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/data-flow/blob/master/LICENSE.md
 * @link    https://github.com/graze/data-flow
 */

namespace Graze\DataFlow\Test\Unit\Flow\Collection;

use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\Collection\Each;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
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

    public function testInvalidInputThrowsAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Each(m::mock(FlowInterface::class));

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
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
