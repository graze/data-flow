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

namespace Graze\DataFlow\Unit\Test\Flow;

use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\Callback;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeInterface;
use Mockery as m;

class CallbackTest extends TestCase
{
    public function testInstanceOf()
    {
        static::assertInstanceOf(FlowInterface::class, new Callback(function ($file) {
        }));
    }

    public function testCallingFlowWillCallTheCallback()
    {
        $called = null;
        $return = m::mock(NodeInterface::class);
        $callbackFlow = new Callback(function ($item) use (&$called, $return) {
            $called = $item;
            return $return;
        });

        $node = m::mock(NodeInterface::class);

        $output = $callbackFlow->flow($node);
        static::assertEquals($return, $output);
        static::assertEquals($called, $node);
    }

    public function testStaticCalling()
    {
        $called = null;
        $return = m::mock(NodeInterface::class);
        $callbackFlow = Flow::callback(function ($item) use (&$called, $return) {
            $called = $item;
            return $return;
        });

        $node = m::mock(NodeInterface::class);

        $output = $callbackFlow->flow($node);
        static::assertEquals($return, $output);
        static::assertEquals($called, $node);
    }

    public function testInvokeFlow()
    {
        $called = null;
        $return = m::mock(NodeInterface::class);
        $callbackFlow = Flow::callback(function ($item) use (&$called, $return) {
            $called = $item;
            return $return;
        });

        $node = m::mock(NodeInterface::class);

        $output = call_user_func($callbackFlow, $node);
        static::assertEquals($return, $output);
        static::assertEquals($called, $node);
    }
}
