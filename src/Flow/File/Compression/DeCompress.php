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

namespace Graze\DataFlow\Flow\File\Compression;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Exception\InvalidCompressionTypeException;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

class DeCompress implements FlowInterface, LoggerAwareInterface
{
    use InvokeTrait;
    use OptionalLoggerTrait;

    /**
     * @var array
     */
    private $options;

    /**
     * Compress constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @param NodeInterface $node
     *
     * @return LocalFile
     * @throws InvalidCompressionTypeException
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof LocalFile)) {
            throw new InvalidArgumentException("Node: $node should be an instance of LocalFile");
        }

        $factory = new CompressionFactory();
        $compressor = $factory->getDeCompressor($node->getCompression());
        $this->log(LogLevel::INFO, "DeCompressing file: '{file}'", ['file' => $node]);
        return $compressor->decompress($node, $this->options);
    }
}
