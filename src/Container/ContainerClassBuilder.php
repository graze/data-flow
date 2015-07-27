<?php

namespace Graze\DataFlow\Container;

use Graze\Extensible\Finder\ClassBuilder\ClassBuilderInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

class ContainerClassBuilder implements ClassBuilderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Build a class from a class name
     *
     * @param string $className
     * @return object
     */
    public function buildClass($className)
    {
        return $this->getContainer()->get($className);
    }
}
