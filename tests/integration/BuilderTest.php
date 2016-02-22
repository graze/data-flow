<?php

namespace Graze\DataFlow\Test\Integration;

use Graze\DataFlow\Builder;
use Graze\DataFlow\Flow;
use Graze\DataFlow\FlowInterface;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;
use Psr\Log\LoggerInterface;

class BuilderTest extends TestCase
{
    /**
     * @var Builder
     */
    private $builder;

    public function setUp()
    {
        $this->builder = new Builder();
    }

    public function testCanBuildAKnownFlow()
    {
        $flow = $this->builder->buildFlow('gzip');
        static::assertInstanceOf(FlowInterface::class, $flow);
    }

    public function testBuildingUnknownFlowWillThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->builder->buildFlow('someUnknownFlowName');
    }

    public function testBuildingValidClassNameButNotAFlowWillThrowAnException()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->builder->buildFlow('flowCollection');
    }

    public function testAddingANamespaceWillSearchThatNamespaceForFlows()
    {
        $this->builder->addNamespace('Graze\\DataFlow\\');

        $flow = $this->builder->buildFlow('flow');

        static::assertInstanceOf(Flow::class, $flow);
    }

    public function testBuildingAFlowWhenALoggerIsSetWillSetTheLoggerOnTheChild()
    {
        $logger = m::mock(LoggerInterface::class);
        $this->builder->setLogger($logger);

        $logger->shouldReceive('log')
               ->times(2);

        $flow = $this->builder->buildFlow('first');

        $collection = new NodeCollection();
        $node = m::mock(NodeInterface::class);
        $collection->add($node);


        $first = $flow->flow($collection);

        static::assertEquals($node, $first);
    }
}
