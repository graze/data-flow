<?php

namespace Graze\DataFlow\Test\Extension;

use Graze\DataFlow\Finder\FlowFinderInterface;
use Graze\DataFlow\Test\Fake\FakeRawFlowable;
use Graze\DataFlow\Test\Fake\FakeNonFlowable;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

class FlowExtensionTraitTest extends TestCase
{
    /**
     * @var FakeRawFlowable
     */
    protected $flowable;

    /**
     * @var FlowFinderInterface
     */
    protected $flowFinder;

    public function setUp()
    {
        $this->flowable = new FakeRawFlowable();
        $this->flowFinder = m::mock('Graze\DataFlow\Finder\FlowFinderInterface');
    }

    public function testCallingAFlowableWithoutProvidingAFinderRaisesAnException()
    {
        static::setExpectedException(
            'Graze\DataFlow\Exception\InvalidFlowableObjectException',
            'The object: Graze\DataFlow\Test\Fake\FakeRawFlowable does not implement FlowableInterface.'
        );

        $this->flowable->doSomething();
    }

    public function testCallingAFlowableCallsTheFlow()
    {
        $this->flowable->setFinder($this->flowFinder);
        $command = 'doSomething';

        $flow = m::mock('Graze\DataFlow\FlowInterface');
        $flow->shouldReceive('flow')->with($this->flowable, ['argument1', 'argument2'])->andReturn(true);
        $this->flowFinder->shouldReceive('findFlow')->with($this->flowable, $command)->andReturn($flow);

        static::assertTrue($this->flowable->doSomething('argument1', 'argument2'));
    }

    public function testCallingAnInvalidCommandThrowsAnException()
    {
        $this->flowable->setFinder($this->flowFinder);
        $this->flowFinder->shouldReceive('findFlow')->with($this->flowable, 'doNothing')->andReturn(null);

        static::setExpectedException(
            'Graze\DataFlow\Exception\InvalidFlowCommandException',
            'The command: doNothing cannot be applied to Graze\DataFlow\Test\Fake\FakeRawFlowable.'
        );

        $this->flowable->doNothing();
    }

    public function testUsingTheTraitNotOnAFlowableObjectWillThrowAnException()
    {
        $flowable = new FakeNonFlowable();

        static::setExpectedException(
            'Graze\DataFlow\Exception\InvalidFlowableObjectException',
            'The object: Graze\DataFlow\Test\Fake\FakeNonFlowable does not implement FlowableInterface.'
        );

        $flowable->something();
    }

    public function testCallingAFlowableReturnsTheResultOfTheFlow()
    {
        $this->flowable->setFinder($this->flowFinder);
        $command = 'doSomething';

        $flow = m::mock('Graze\DataFlow\FlowInterface');
        $flow->shouldReceive('flow')->with($this->flowable, ['argument1', 'argument2'])->andReturn(true, false);
        $this->flowFinder->shouldReceive('findFlow')->with($this->flowable, $command)->andReturn($flow);

        static::assertTrue($this->flowable->doSomething('argument1', 'argument2'));
        static::assertFalse($this->flowable->doSomething('argument1', 'argument2'));
    }
}
