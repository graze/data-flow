<?php

namespace Graze\DataFlow\Test\Unit\Flowable\Finder;

use Graze\DataFlow\Flowable\Finder\SimpleRegisteredFinder;
use Graze\DataFlow\Test\Flowable\Fake\FakeFlow;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SimpleRegisteredFinderTest extends TestCase
{
    /**
     * @var SimpleRegisteredFinder
     */
    protected $finder;

    public function setUp()
    {
        $this->finder = new SimpleRegisteredFinder();
        FakeFlow::$canFlow = true;
    }

    public function tearDown()
    {
        FakeFlow::$canFlow = true;
    }

    public function testCanFindFlowAfterAdding()
    {
        static::assertTrue(
            $this->finder->addFlow('test', 'Graze\DataFlow\Test\Flowable\Fake\FakeFlow'),
            "Should be able to add a flow"
        );

        $flow = $this->finder->get(m::mock('Graze\DataFlow\Flowable\FlowableInterface'), 'test');

        static::assertNotNull($flow, "Found flow should not be null");

        static::assertInstanceOf('Graze\DataFlow\Test\Flowable\Fake\FakeFlow', $flow, "Built flow should be of type: FakeFlow");
    }

    public function testUnableToAddSameFlowCommandTwice()
    {
        static::assertTrue(
            $this->finder->addFlow('test2', 'Graze\DataFlow\Test\Flowable\Fake\FakeFlow'),
            "Should be able to add the first flow"
        );

        static::assertFalse(
            $this->finder->addFlow('test2', 'Graze\DataFlow\Test\Flowable\Fake\FakeFlow'),
            "Should not be able to add a second flow with the same name"
        );
    }

    public function testFindingAFlowWhenNoNameIsRegistered()
    {
        static::assertNull(
            $this->finder->get(m::mock('Graze\DataFlow\Flowable\FlowableInterface'), 'test'),
            "Should not be able to find flow as it is not registered"
        );

        static::assertTrue(
            $this->finder->addFlow('test', 'Graze\DataFlow\Test\Flowable\Fake\FakeFlow'),
            "Should be able to add a flow"
        );

        static::assertNull(
            $this->finder->get(m::mock('Graze\DataFlow\Flowable\FlowableInterface'), 'test2'),
            "Should not be able to find a flow with the wrong name"
        );
    }

    public function testFindingAFlowWithAClassThatIsNotAFlowInterfaceRaisesAnException()
    {
        $fakeClass = m::mock('overload:Graze\DataFlow\Flowable\DummyClass');

        $this->finder->addFlow('dummy', 'Graze\DataFlow\Flowable\DummyClass');

        static::setExpectedException(
            'Graze\DataFlow\Flowable\Exception\InvalidFlowObjectException',
            'The flow class specified in: Graze\DataFlow\Flowable\DummyClass does not implement FlowInterface.'
        );

        $this->finder->get(m::mock('Graze\DataFlow\Flowable\FlowableInterface'), 'dummy');
    }

    public function testFindingAFlowThatCannotHandleTheNodeWillReturnNull()
    {
        $node = m::mock('Graze\DataFlow\Flowable\FlowableInterface');

        FakeFlow::$canFlow = false;

        $this->finder->addFlow('noflow', 'Graze\DataFlow\Test\Flowable\Fake\FakeFlow');
        static::assertNull(
            $this->finder->get($node, 'noflow'),
            "The Flow should not be able to handle the node specified"
        );
    }
}
