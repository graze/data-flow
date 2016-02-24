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

namespace Graze\DataFlow\Test\Unit;

use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFlow\Flow;
use Graze\DataFlow\FlowBuilderInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeInterface;
use Mockery as m;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FlowTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testFluentChain()
    {
        $node1 = m::mock(FileNodeInterface::class);
        $node2 = m::mock(NodeInterface::class);

        $collection = new NodeCollection([$node1, $node2]);

        $result = Flow::create()
                      ->filter(function (NodeInterface $node) {
                          return ($node instanceof FileNodeInterface);
                      })
                      ->first()
                      ->flow($collection);

        static::assertSame($result, $node1);
    }

    public function testSetLoggerSetsTheLoggerOnTheBuilder()
    {
        $builder = m::mock(FlowBuilderInterface::class, LoggerAwareInterface::class);
        $logger = m::mock(LoggerInterface::class);

        Flow::setBuilder($builder);
        $builder->shouldReceive('setLogger')
                ->with($logger)
                ->once();

        Flow::useLogger($logger);
    }

    public function testWithWillCallAddNamespaceOnBuilder()
    {
        $builder = m::mock(FlowBuilderInterface::class);
        Flow::setBuilder($builder);

        $builder->shouldReceive('addNamespace')
                ->with('Graze\\DataFlow\\')
                ->once();

        Flow::with('Graze\\DataFlow\\');
    }
}
