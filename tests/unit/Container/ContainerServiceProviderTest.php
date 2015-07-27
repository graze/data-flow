<?php

namespace Graze\DataFlow\Test\Unit\Container;

use Graze\DataFlow\Container\ContainerServiceProvider;
use Graze\DataFlow\Container\HierarchyServiceProvider;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

class ContainerServiceProviderTest extends TestCase
{
    /**
     * @var HierarchyServiceProvider
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new ContainerServiceProvider();
    }

    public function testCallingProvidesWillListTheProvidedAliases()
    {
        $expected = [
            'Graze\Extensible\Finder\ClassBuilder\ClassBuilderInterface',
        ];

        $actual = $this->provider->provides();

        static::assertEquals($expected, $actual);
    }

    public function testCallingProvidesWithAnAliasWillReturnTrueOrFalse()
    {
        static::assertTrue($this->provider->provides('Graze\Extensible\Finder\ClassBuilder\ClassBuilderInterface'));
        static::assertFalse($this->provider->provides('Some\Non\Existent\Class\Here'));
    }

    public function testCallingRegisterWillCallAddForEachProvides()
    {
        $container = m::mock('League\Container\ContainerInterface');

        $classDefinition = m::mock('League\Container\Definition\ClassDefinitionInterface');
        $classDefinition->shouldReceive('withMethodCall')
                        ->with('setContainer', [$container])
                        ->once();

        $container->shouldReceive('add')
                  ->with(
                      'Graze\Extensible\Finder\ClassBuilder\ClassBuilderInterface',
                      'Graze\DataFlow\Container\ContainerClassBuilder'
                  )
                  ->once()
                  ->andReturn($classDefinition);
        $container->shouldReceive('add')
                  ->with(
                      'Graze\Extensible\Finder\Discovery\DiscoveryInterface',
                      'Graze\Extensible\Finder\Discovery\AutoDiscoverer'
                  )
                  ->once();
        $container->shouldReceive('add')
                  ->with(
                      'Graze\Extensible\Finder\Reflection\ReflectionHelperInterface',
                      'Graze\Extensible\Finder\Reflection\ReflectionHelper'
                  )
                  ->once();

        $this->provider->setContainer($container);
        $this->provider->register();
    }
}
