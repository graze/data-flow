<?php

namespace Graze\DataFlow\Flowable\Finder;

use Graze\DataFlow\Flow\FlowInterface;
use Graze\DataFlow\Flowable\Exception\InvalidFlowObjectException;
use Graze\DataFlow\Flowable\FlowableInterface;

/**
 * Class SimpleRegisteredFinder
 *
 * The Flow manager
 *
 * @package Graze\DataFlow
 */
class SimpleRegisteredFinder implements FlowFinderInterface
{
    /**
     * @var string[]
     */
    protected $flows;

    /**
     * Manually add flows to the simple finder
     *
     * @param string $command
     * @param string $className
     * @return bool
     */
    public function addFlow($command, $className)
    {
        if (!isset($this->flows[$command])) {
            $this->flows[$command] = $className;
            return true;
        }

        return false;
    }

    /**
     * @param FlowableInterface $flowable
     * @param string            $method
     * @return FlowInterface|null null if no command can be found
     * @throws InvalidFlowObjectException
     */
    public function get(FlowableInterface $flowable, $method)
    {
        if (!isset($this->flows[$method])) {
            return null;
        }

        $className = $this->flows[$method];
        $class = new $className();
        if (!($class instanceof FlowInterface)) {
            throw new InvalidFlowObjectException($className);
        }

        if ($class->canFlow($flowable, $method)) {
            return $class;
        } else {
            return null;
        }
    }
}
