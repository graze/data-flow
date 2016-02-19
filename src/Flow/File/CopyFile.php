<?php

namespace Graze\DataFlow\Flow\File;

use Graze\DataFile\Modify\Transfer\Transfer;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

class CopyFile extends Transfer implements FlowInterface
{
    use InvokeTrait;

    /**
     * @var FileNodeInterface
     */
    private $target;

    /**
     * CopyFile constructor.
     *
     * @param FileNode $target
     */
    public function __construct(FileNode $target)
    {
        $this->target = $target;
    }

    /**
     * @param NodeInterface $node
     *
     * @return FileNode
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof FileNode)) {
            throw new InvalidArgumentException("Node: $node should be an instance of FileNode");
        }

        if (substr($this->target->getPath(), -1) == '/') {
            $target = $this->target->getClone()->setPath($this->target->getPath() . $node->getFilename());
        } else {
            $target = $this->target;
        }

        return $this->copyTo($node, $target);
    }
}
