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
