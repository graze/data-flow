<?php

/**
 * Bootstrap file for ContainerExtensible
 *
 * It still uses AutoDiscovery, but Flows are built using the container so can auto-resolve dependencies
 */

namespace Graze\DataFlow\Container;

use League\Container\Container;

$container = new Container();

$container->addServiceProvider(new ContainerServiceProvider());

ContainerFacade::setContainer($container);
