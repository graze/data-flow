<?php
/**
 * This file is part of graze/data-flow
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/data-flow/blob/master/LICENSE.md
 * @link    https://github.com/graze/data-flow
 */

namespace Graze\DataFlow;

use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFlow\Flow\Runner\Run;
use Graze\DataNode\NodeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use ReflectionClass;

/**
 * Flow Facade
 *
 * @method static Flow run(FlowInterface ...$flows)
 * @method static Flow toAll(FlowInterface ...$flows)
 * @method static Flow first(callable $fn = null)
 * @method static Flow last(callable $fn = null)
 * @method static Flow filter(callable $fn = null)
 * @method static Flow map(callback $fn = null)
 * @method static Flow each(FlowInterface $flow)
 * @method static Flow callback(callable $fn = null)
 * @method static Flow makeDirectory($mode = 0777)
 * @method static Flow merge(FileNodeInterface $file, array $options = [])
 * @method static Flow compress($type, array $options = [])
 * @method static Flow decompress(array $options = [])
 * @method static Flow gzip()
 * @method static Flow gunzip()
 * @method static Flow zip()
 * @method static Flow unzip()
 * @method static Flow copyFile(FileNodeInterface $target);
 * @method static Flow copyFiles(FileNodeInterface $target);
 * @method static Flow moveFile(FileNodeInterface $target);
 * @method static Flow moveFiles(FileNodeInterface $target);
 * @method static Flow convertEncoding($newEncoding)
 * @method static Flow replaceText($from, $to)
 * @method static Flow tail($lines)
 * @method static Flow head($lines)
 */
class Flow extends Run
{
    /**
     * @inheritdoc
     */
    public function flow(NodeInterface $node)
    {
        $this->log(LogLevel::NOTICE, "Running through {count} flows", ['count' => count($this->items)]);

        $current = $node;
        foreach ($this->items as $flow) {
            $current = $flow->flow($current);
        }
        return $current;
    }

    /**
     * @var FlowBuilderInterface
     */
    protected static $builder;

    /**
     * @return FlowBuilderInterface
     */
    protected static function getBuilder()
    {
        if (!static::$builder instanceof FlowBuilderInterface) {
            static::$builder = new Builder();
        }
        return static::$builder;
    }

    /**
     * @param FlowBuilderInterface $builder
     */
    public static function setBuilder(FlowBuilderInterface $builder)
    {
        static::$builder = $builder;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return null|void
     */
    public static function useLogger(LoggerInterface $logger)
    {
        $builder = static::getBuilder();

        if ($builder instanceof LoggerAwareInterface) {
            $builder->setLogger($logger);
        }
    }

    /**
     * @param string $namespace
     */
    public static function with($namespace)
    {
        self::getBuilder()->addNamespace($namespace);
    }

    /**
     * @param string $flowName
     * @param array  $arguments
     *
     * @return Flow
     */
    public static function __callStatic($flowName, $arguments)
    {
        $flow = new static();
        return $flow->__call($flowName, $arguments);
    }

    /**
     * @param string $flowSpec
     * @param array  $arguments
     *
     * @return Flow
     */
    public static function buildFlow($flowSpec, $arguments = [])
    {
        return static::getBuilder()->buildFlow($flowSpec, $arguments);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return self
     */
    public function __call($method, $arguments)
    {
        return $this->add(static::buildFlow($method, $arguments));
    }

    /**
     * Create instance validator.
     *
     * @return Flow
     */
    public static function create()
    {
        $ref = new ReflectionClass(__CLASS__);
        return $ref->newInstanceArgs(func_get_args());
    }
}
