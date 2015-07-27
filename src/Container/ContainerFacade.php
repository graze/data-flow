<?php

namespace Graze\DataFlow\Container;

use League\Container\ContainerInterface;

class ContainerFacade
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        return static::$container;
    }
}
