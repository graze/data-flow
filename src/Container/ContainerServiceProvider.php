<?php

namespace Graze\DataFlow\Container;

use League\Container\ServiceProvider;

class ContainerServiceProvider extends ServiceProvider
{

    /**
     * {@inheritDoc}
     */
    protected $provides = [
        'Graze\Extensible\Finder\ClassBuilder\ClassBuilderInterface',
        'Graze\Extensible\Finder\Discovery\DiscoveryInterface',
        'Graze\Extensible\Finder\Reflection\ReflectionHelperInterface',
        'Graze\DataFlow\Utility\Process\ProcessFactoryInterface',
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->add(
            'Graze\Extensible\Finder\ClassBuilder\ClassBuilderInterface',
            'Graze\DataFlow\Container\ContainerClassBuilder'
        )->withMethodCall('setContainer', [$this->container]);
        $this->container->add(
            'Graze\Extensible\Finder\Discovery\DiscoveryInterface',
            'Graze\Extensible\Finder\Discovery\AutoDiscoverer'
        );
        $this->container->add(
            'Graze\Extensible\Finder\Reflection\ReflectionHelperInterface',
            'Graze\Extensible\Finder\Reflection\ReflectionHelper'
        );
        $this->container->add(
            'Graze\DataFlow\Utility\Process\ProcessFactoryInterface',
            'Graze\DataFlow\Utility\Process\ProcessFactory'
        );
    }
}
