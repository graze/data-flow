<?php

namespace Graze\DataFlow\Extension;

use Graze\DataFlow\Exception\InvalidFlowableObjectException;
use Graze\DataFlow\Exception\InvalidFlowCommandException;
use Graze\DataFlow\FlowableInterface;

/**
 * Class FlowExtension
 *
 * This trait (when applied to a FlowableInterface extends the object to allow extension calls
 *
 * @package Graze\DataFlow\Extension
 */
trait FlowExtension
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws InvalidFlowCommandException
     * @throws InvalidFlowableObjectException
     */
    function __call($name, $arguments)
    {
        if (!($this instanceof FlowableInterface)) {
            throw new InvalidFlowableObjectException(get_class($this));
        }

        $finder = $this->getFinder();
        if (is_null($finder)) {
            throw new InvalidFlowableObjectException(get_class($this), 'The provided flow finder does not exist');
        }

        $flow = $finder->findFlow($this, $name);

        if (is_null($flow)) {
            throw new InvalidFlowCommandException($name, get_class($this));
        }

        return $flow->flow($this, $arguments);
    }
}
