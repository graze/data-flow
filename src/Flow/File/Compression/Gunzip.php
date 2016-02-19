<?php

namespace Graze\DataFlow\Flow\File\Compression;

use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

class Gunzip extends \Graze\DataFile\Modify\Compress\Gzip implements FlowInterface
{
    use InvokeTrait;

    /**
     * Gzip constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Run a 'flow' through the handler
     *
     * @param NodeInterface $node
     *
     * @return LocalFile
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof LocalFile)) {
            throw new InvalidArgumentException("Node: $node should be an instance of LocalFile");
        }

        return $this->gunzip($node, $this->options);
    }
}
