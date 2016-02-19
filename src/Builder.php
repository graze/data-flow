<?php

namespace Graze\DataFlow;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use ReflectionClass;

class Builder implements FlowBuilderInterface
{
    use OptionalLoggerTrait;

    /**
     * @var string[]
     */
    protected $namespaces = [
        'Graze\\DataFlow\\Flow\\',
        'Graze\\DataFlow\\Flow\\Collection\\',
        'Graze\\DataFlow\\Flow\\File\\',
        'Graze\\DataFlow\\Flow\\File\\Compression\\',
        'Graze\\DataFlow\\Flow\\Runner\\',
    ];

    /**
     * Add a namespace to check for flow names within
     *
     * @param string $namespace
     *
     * @return void
     */
    public function addNamespace($namespace)
    {
        $this->namespaces[] = $namespace;
    }

    /**
     * @return string[]
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Create a new Flow based on a name
     *
     * @param mixed $flowName
     * @param array $arguments
     *
     * @return FlowInterface
     */
    public function buildFlow($flowName, array $arguments = [])
    {
        if ($flowName instanceof FlowInterface) {
            return $flowName;
        }
        foreach ($this->getNamespaces() as $namespace) {
            $className = $namespace . ucfirst($flowName);
            if (!class_exists($className)) {
                continue;
            }
            $reflection = new ReflectionClass($className);
            if (!$reflection->isSubclassOf(FlowInterface::class)) {
                throw new InvalidArgumentException(sprintf(
                    "'%s' from flowName: '%s' is not a valid DataFlow",
                    $className,
                    $flowName
                ));
            }
            $this->log(LogLevel::DEBUG, "Building flow: {class}", ['class' => $className]);
            $flow = $reflection->newInstanceArgs($arguments);
            if ($this->logger && ($flow instanceof LoggerAwareInterface)) {
                $flow->setLogger($this->logger);
            }
            return $flow;
        }
        throw new InvalidArgumentException(sprintf("'%s' is not a valid flow name", $flowName));
    }
}
