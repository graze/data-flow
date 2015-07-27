<?php

namespace Graze\DataFlow\Test\Container\Fake;

use Graze\DataFlow\Container\ContainerExtensible;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\Finder\FinderAwareInterface;
use League\Container\ContainerAwareInterface;

class FakeContainerExtensible implements ExtensibleInterface, ContainerAwareInterface, FinderAwareInterface
{
    use ContainerExtensible;
}
