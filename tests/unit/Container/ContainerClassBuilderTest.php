<?php

namespace Graze\DataFlow\Test\Unit\Container;

use Graze\DataFlow\Container\ContainerClassBuilder;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

class ContainerClassBuilderTest extends TestCase
{
    public function testInstanceOf()
    {
        $builder = new ContainerClassBuilder();

        static::assertInstanceOf('Graze\Extensible\Finder\ClassBuilder\ClassBuilderInterface', $builder);
        static::assertInstanceOf('League\Container\ContainerAwareInterface', $builder);
    }

    public function testBuildClassCallsContainer()
    {
        $container = m::mock('League\Container\ContainerInterface');

        $builder = new ContainerClassBuilder();
        $builder->setContainer($container);

        $object = m::mock('stdClass');

        $container->shouldReceive('get')
            ->with('Class\To\Build')
            ->andReturn($object);

        static::assertSame($object, $builder->buildClass('Class\To\Build'));
    }
}
