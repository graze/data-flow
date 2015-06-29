<?php

namespace Graze\DataFlow\Finder;

use Graze\DataFlow\Exception\InvalidFlowObjectException;
use Graze\DataFlow\FlowableInterface;
use Graze\DataFlow\FlowInterface;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\ServiceProvider;

/**
 * Class ContainerFlowFinder
 *
 * The Container Flow Finder uses League\Container to resolve the flows
 *
 * @package Graze\DataFlow\Finder
 */
class ContainerFlowFinder Implements FlowFinderInterface
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * @var FlowFinderInterface
     */
    private static $instance;

    /**
     * @param FlowableInterface $flowable
     * @param string            $command
     * @return FlowInterface
     * @throws InvalidFlowObjectException
     */
    public function findFlow(FlowableInterface $flowable, $command)
    {
        if (!self::$container->isRegistered($command)) {
            return null;
        }

        $flow = self::$container->get($command);

        if (!($flow instanceof FlowInterface)) {
            throw new InvalidFlowObjectException(get_class($flow));
        }

        if ($flow->canFlow($flowable)) {
            return $flow;
        } else {
            return null;
        }
    }

    /**
     * @param ServiceProvider|null $defaultFlowProvider
     * @return ContainerFlowFinder
     */
    public static function getInstance($defaultFlowProvider = null)
    {
        if (is_null(static::$instance)) {
            $defaultFlowProvider = $defaultFlowProvider ?: new DefaultFlowsServiceProvider();
            static::addProvider($defaultFlowProvider);
            self::$instance = new ContainerFlowFinder();
        }
        return self::$instance;
    }

    /**
     * Add a service provider to the container
     *
     * @param ServiceProvider $provider
     */
    public static function addProvider(ServiceProvider $provider)
    {
        if (is_null(self::$container)) {
            self::$container = new Container();
        }
        self::$container->addServiceProvider($provider);
    }
}
