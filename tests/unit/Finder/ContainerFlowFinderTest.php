<?php

namespace Graze\DataFlow\Test\Finder;

use Graze\DataFlow\Finder\ContainerFlowFinder;
use Graze\DataFlow\Test\Fake\FakeFlow;
use Graze\DataFlow\Test\TestCase;
use League\Container\Container;
use League\Container\ServiceProvider;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ContainerFlowFinderTest extends TestCase
{
    /**
     * @var ContainerFlowFinder
     */
    protected $finder;

    /**
     * @var ServiceProvider
     */
    protected $serviceProvider;

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->serviceProvider = m::mock('League\Container\ServiceProvider');
        $this->container = m::mock('overload:League\Container\Container');
        $this->container->shouldReceive('addServiceProvider');
    }

    public function testCanFindFlowAfterAdding()
    {
        $this->container->shouldReceive('isRegistered')->with('test')->andReturn(true);
        $this->container->shouldReceive('get')->with('test')->andReturn(new FakeFlow());
        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);
        $flow = $finder->findFlow(m::mock('Graze\DataFlow\FlowableInterface'), 'test');

        static::assertNotNull($flow, "Found flow should not be null");

        static::assertInstanceOf('Graze\DataFlow\Test\Fake\FakeFlow', $flow, "Built flow should be of type: FakeFlow");
    }

    public function testFindingAFlowWhenNoNameIsRegistered()
    {
        $this->container->shouldReceive('isRegistered')->with('test')->andReturn(false);

        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);

        static::assertNull(
            $finder->findFlow(m::mock('Graze\DataFlow\FlowableInterface'), 'test'),
            "Should not be able to find flow as it is not registered"
        );
    }

    public function testFindingAFlowWithAClassThatIsNotAFlowInterfaceRaisesAnException()
    {
        $dummy = m::mock('overload:Graze\DataFlow\DummyClass');

        $this->container->shouldReceive('isRegistered')->with('dummy')->andReturn(true);
        $this->container->shouldReceive('get')->with('dummy')->andReturn(new \Graze\DataFlow\DummyClass());
        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);

        static::setExpectedException(
            'Graze\DataFlow\Exception\InvalidFlowObjectException',
            'The flow class specified in: Graze\DataFlow\DummyClass does not implement FlowInterface.'
        );

        $finder->findFlow(m::mock('Graze\DataFlow\FlowableInterface'), 'dummy');
    }

    public function testFindingAFlowThatCannotHandleTheNodeWillReturnNull()
    {
        $node = m::mock('Graze\DataFlow\FlowableInterface');

        FakeFlow::$canFlow = false;

        $this->container->shouldReceive('isRegistered')->with('noflow')->andReturn(true);
        $this->container->shouldReceive('get')->with('noflow')->andReturn(new FakeFlow());
        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);

        static::assertNull(
            $finder->findFlow($node, 'noflow'),
            "The Flow should not be able to handle the node specified"
        );
    }

    public function testDefaultFlowsServiceProviderGetsInjectedIntoContainer()
    {
        $this->container->shouldReceive('addServiceProvider')->with(m::type('Graze\DataFlow\Finder\DefaultFlowsServiceProvider'));
        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);
    }
}
