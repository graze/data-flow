<?php

namespace Graze\DataFlow\Flow\File;

use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

class Head extends \Graze\DataFile\Modify\Head implements FlowInterface
{
    use InvokeTrait;

    /**
     * @param string $lines   Number of lines to tail (accepts +/- modifiers)
     * @param array  $options List of options:
     *                        -postfix <string> (Default: tail)
     *                        -keepOldFile <bool> (Default: true)
     */
    public function __construct($lines, array $options = [])
    {
        $this->lines = $lines;
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

        return $this->head($node, $this->lines, $this->options);
    }
}
