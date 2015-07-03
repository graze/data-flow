<?php

namespace Graze\DataFlow\Test\Unit\Flowable\Finder;

use Graze\DataFlow\Flowable\Finder\ContainerFlowFinder;
use Graze\DataFlow\Test\Flowable\Fake\FakeFlow;
use Graze\DataFlow\Test\Flowable\Fake\FakeFlowable;
use Graze\DataFlow\Test\TestCase;
use League\Container\Container;
use League\Container\Exception\ReflectionException;
use League\Container\ServiceProvider;
use Mockery as m;
use Mockery\MockInterface;

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
     * @var Container|MockInterface
     */
    protected $container;

    public function setUp()
    {
        $this->serviceProvider = m::mock('League\Container\ServiceProvider');
        $this->container = m::mock('overload:League\Container\Container');
        $this->container->shouldReceive('addServiceProvider');
        FakeFlow::$canFlow = true;
    }

    public function testCanFindFlowAfterAdding()
    {
        $this->container->shouldReceive('isRegistered')->with('test')->andReturn(true);
        $this->container->shouldReceive('get')->with('test')->andReturn(new FakeFlow());
        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);
        $flow = $finder->get(m::mock('Graze\DataFlow\Flowable\FlowableInterface'), 'test');

        static::assertNotNull($flow, "Found flow should not be null");

        static::assertInstanceOf('Graze\DataFlow\Test\Flowable\Fake\FakeFlow', $flow, "Built flow should be of type: FakeFlow");
    }

    public function testFindingAFlowWhenNoNameIsRegistered()
    {
        $this->container->shouldReceive('get')->with('test')->andThrow(new ReflectionException());
        $this->container->shouldReceive('get')->with('/.+::test$/')->andThrow(new ReflectionException());

        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);

        static::assertNull(
            $finder->get(m::mock('Graze\DataFlow\Flowable\FlowableInterface'), 'test'),
            "Should not be able to find flow as it is not registered"
        );
    }

    public function testFindingAFlowWithAClassThatIsNotAFlowInterfaceRaisesAnException()
    {
        $dummy = m::mock('overload:Graze\DataFlow\Flowable\DummyClass');

        $this->container->shouldReceive('get')->with('dummy')->andReturn(new \Graze\DataFlow\Flowable\DummyClass());
        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);

        static::setExpectedException(
            'Graze\DataFlow\Flowable\Exception\InvalidFlowObjectException',
            'The flow class specified in: Graze\DataFlow\Flowable\DummyClass does not implement FlowInterface.'
        );

        $finder->get(m::mock('Graze\DataFlow\Flowable\FlowableInterface'), 'dummy');
    }

    public function testFindingAFlowThatCannotHandleTheNodeWillReturnNull()
    {
        $node = m::mock('Graze\DataFlow\Flowable\FlowableInterface');

        FakeFlow::$canFlow = false;

        $this->container->shouldReceive('get')->with('noflow')->andReturn(new FakeFlow());
        $this->container->shouldReceive('get')->with('/.*::noflow$/')->andReturn(new FakeFlow());
        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);

        static::assertNull(
            $finder->get($node, 'noflow'),
            "The Flow should not be able to handle the node specified"
        );
    }

    public function testDefaultFlowsServiceProviderGetsInjectedIntoContainer()
    {
        $this->container->shouldReceive('addServiceProvider')->with(m::type('Graze\DataFlow\Flowable\Finder\DefaultFlowsServiceProvider'));
        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);
    }

    public function testGetSearchesThroughAllParentsAndInterfacesForMatch() {
        $this->container->shouldReceive('get')->with('failure')->once()->andThrow(new ReflectionException());
        $this->container->shouldReceive('get')->with('Graze\DataFlow\Test\Flowable\Fake\FakeFlowable::failure')->once()->andThrow(new ReflectionException());
        $this->container->shouldReceive('get')->with('Graze\DataFlow\Flowable\Flowable::failure')->once()->andThrow(new ReflectionException());
        $this->container->shouldReceive('get')->with('Graze\DataFlow\Flowable\FlowableInterface::failure')->once()->andThrow(new ReflectionException());

        $flowable = new FakeFlowable();

        $finder = ContainerFlowFinder::getInstance($this->serviceProvider);

        static::assertNull(
            $finder->get($flowable, 'failure'),
            "The Flow should not be able to handle the node specified"
        );
    }
}
