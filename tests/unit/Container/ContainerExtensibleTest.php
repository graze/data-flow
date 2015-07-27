<?php

namespace Graze\DataFlow\Test\Unit\Container;

use Graze\DataFlow\Container\ContainerExtensible;
use Graze\DataFlow\Container\ContainerFacade;
use Graze\DataFlow\Test\Container\Fake\FakeContainerExtensible;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ContainerExtensibleTest extends TestCase
{
    public function testInstanceOf()
    {
        $extensible = new FakeContainerExtensible();

        static::assertInstanceOf('Graze\Extensible\ExtensibleInterface', $extensible);
        static::assertInstanceOf('League\Container\ContainerAwareInterface', $extensible);
    }

    public function testGetFinderCallsContainerToFindFinder()
    {
        $container = m::mock('League\Container\Container');

        $extensible = new FakeContainerExtensible();
        $extensible->setContainer($container);

        $finder = m::mock('Graze\Extensible\Finder\ExtensionFinderInterface');

        $container->shouldReceive('get')
            ->with('Graze\Extensible\Finder\DocBlockExtensionFinder')
            ->andReturn($finder);

        $foundFinder = $extensible->getFinder();

        static::assertSame($finder, $foundFinder);
    }

    public function testGetContainerFromStaticContainer()
    {
        $container = m::mock('League\Container\Container');

        ContainerFacade::setContainer($container);

        $extensible = new FakeContainerExtensible();

        static::assertSame($container, $extensible->getContainer());
    }
}
