<?php

namespace Graze\DataFlow\Test\Unit\Flowable\Finder;

use Graze\DataFlow\Flowable\Finder\DefaultFlowsServiceProvider;
use Graze\DataFlow\Test\TestCase;
use League\Container\Container;
use Mockery as m;
use Mockery\MockInterface;

class DefaultFlowsServiceProviderTest extends TestCase
{
    /**
     * @var DefaultFlowsServiceProvider
     */
    protected $provider;

    /**
     * @var Container|MockInterface
     */
    protected $container;

    public function setUp()
    {
        $this->container = m::mock('League\Container\Container');
        $this->provider = new DefaultFlowsServiceProvider();
        $this->provider->setContainer($this->container);
    }

    public function testCallingRegisterWillRegisterClassesWithTheContainer()
    {
        $this->container->shouldReceive('add')->atLeast()->once();

        $this->provider->register();
    }
}
