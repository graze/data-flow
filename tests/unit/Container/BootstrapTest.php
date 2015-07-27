<?php

namespace Graze\DataFlow\Test\Unit\Container;

use Graze\DataFlow\Container\ContainerExtensible;
use Graze\DataFlow\Container\ContainerFacade;
use Graze\DataFlow\Test\TestCase;
use League\Container\Container;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class BootstrapTest extends TestCase
{
    public function setUp()
    {
        require __DIR__ . '/../../../src/Container/bootstrap.php';
    }

    public function testContainerIsRegistered()
    {
        $container = ContainerFacade::getContainer();

        static::assertNotNull($container);
        static::assertInstanceOf('League\Container\ContainerInterface', $container);
    }

    public function testContainerHasDefaultServiceProvider()
    {
        $container = ContainerFacade::getContainer();

        $object = $container->get('Graze\Extensible\Finder\ClassBuilder\ClassBuilderInterface');

        static::assertInstanceOf('Graze\DataFlow\Container\ContainerClassBuilder', $object);
    }
}
