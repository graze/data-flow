<?php

namespace Graze\DataFlow\Flowable\Extension;

use Graze\DataFlow\Flowable\Exception\InvalidFlowableObjectException;
use Graze\DataFlow\Flowable\Exception\InvalidFlowCommandException;
use Graze\DataFlow\Flowable\FlowableInterface;

/**
 * Class FlowExtension
 *
 * This trait (when applied to a FlowableInterface extends the object to allow extension calls
 *
 * @package Graze\DataFlow\Flowable\Extension
 */
trait FlowExtension
{
    /**
     * @param string $name      Command
     * @param array  $arguments Array of arguments
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

        $flow = $finder->get($this, $name);

        if (is_null($flow)) {
            throw new InvalidFlowCommandException($name, get_class($this));
        }

        $callArguments = array_merge([$this], $arguments);

        return call_user_func_array([$flow, $name], $callArguments);
    }
}
