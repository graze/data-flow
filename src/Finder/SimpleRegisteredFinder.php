<?php

namespace Graze\DataFlow\Finder;

use Graze\DataFlow\Exception\InvalidFlowObjectException;
use Graze\DataFlow\FlowableInterface;
use Graze\DataFlow\FlowInterface;

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
     * @param string            $command
     * @return FlowInterface|null null if no command can be found
     * @throws InvalidFlowObjectException
     */
    public function findFlow(FlowableInterface $flowable, $command)
    {
        if (!isset($this->flows[$command])) {
            return null;
        }

        $className = $this->flows[$command];
        $class = new $className();
        if (!($class instanceof FlowInterface)) {
            throw new InvalidFlowObjectException($className);
        }

        if ($class->canFlow($flowable)) {
            return $class;
        } else {
            return null;
        }
    }
}
