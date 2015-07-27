<?php

namespace Graze\DataFlow\Container;

use Graze\Extensible\Finder\ExtensionFinderInterface;
use Graze\Extensible\IsExtensible;
use League\Container\ContainerInterface;

trait ContainerExtensible
{
    use IsExtensible;

    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var ExtensionFinderInterface
     */
    protected $finder = null;

    /**
     * @return ExtensionFinderInterface
     */
    public function getFinder()
    {
        if (is_null($this->finder)) {
            $this->finder = $this->getContainer()->get('Graze\Extensible\Finder\DocBlockExtensionFinder');
        }
        return $this->finder;
    }

    /**
     * @inheritDoc
     */
    public function getContainer()
    {
        if (is_null($this->container)) {
            $this->container = ContainerFacade::getContainer();
        }

        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container)
    {
        if (!is_null(ContainerFacade::getContainer())) {
            ContainerFacade::setContainer($container);
        }
        $this->container = $container;

        return $this;
    }
}
