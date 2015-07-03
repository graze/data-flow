<?php

namespace Graze\DataFlow\Flowable\Finder;

use Graze\DataFlow\Flow\FlowInterface;
use Graze\DataFlow\Flowable\Exception\InvalidFlowObjectException;
use Graze\DataFlow\Flowable\FlowableInterface;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\Exception\ReflectionException;
use League\Container\ServiceProvider;
use ReflectionClass;

/**
 * Class ContainerFlowFinder
 *
 * The Container Flow Finder uses League\Container to resolve the flows
 *
 * @package Graze\DataFlow\Flowable\Finder
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
     * @param string            $method
     * @return mixed|null
     * @throws InvalidFlowObjectException
     */
    private function getFlowFromContainer(FlowableInterface $flowable, $method)
    {
        try {
            $flow = self::$container->get($method);
        } catch (ReflectionException $e) {
            return null;
        }

        if (!($flow instanceof FlowInterface)) {
            throw new InvalidFlowObjectException(get_class($flow));
        }

        if ($flow->canFlow($flowable, $this->getMethodName($method))) {
            return $flow;
        } else {
            return null;
        }
    }

    /**
     * @param object $object
     * @return \string[]
     */
    private function getParentClasses($object)
    {
        $parent = new ReflectionClass($object);
        $parents = [];
        do {
            $parents[] = $parent->getName();
            $interfaces = $parent->getInterfaceNames();
            $parents = array_unique(array_merge($parents, $interfaces));
        } while ($parent = $parent->getParentClass());
        return $parents;
    }

    /**
     * Extract the method name from the provided method and class
     *
     * @param string $fullMethod
     * @return string
     */
    private function getMethodName($fullMethod)
    {
        if (stripos($fullMethod, '::')) {
            return explode('::', $fullMethod)[1];
        }
        return $fullMethod;
    }

    /**
     * @param FlowableInterface $flowable
     * @param string            $method
     * @return FlowInterface
     * @throws InvalidFlowObjectException
     */
    public function get(FlowableInterface $flowable, $method)
    {
        $flow = $this->getFlowFromContainer($flowable, $method);

        if (is_null($flow)) {
            $parents = $this->getParentClasses($flowable);
            foreach ($parents as $parent) {
                $flow = $this->getFlowFromContainer($flowable, "$parent::$method");
                if (!is_null($flow)) {
                    break;
                }
            }
        }

        return $flow;
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
