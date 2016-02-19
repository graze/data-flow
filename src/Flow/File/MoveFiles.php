<?php

namespace Graze\DataFlow\Flow\File;

use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFlow\Flow\AbstractFlow;
use Graze\DataFlow\Flow\Collection\Each;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

class MoveFiles extends AbstractFlow
{
    /**
     * @var FileNode
     */
    private $targetDirectory;

    /**
     * MoveFiles constructor.
     *
     * @param FileNode $targetDirectory
     */
    public function __construct(FileNode $targetDirectory)
    {
        if (substr($targetDirectory, -1) != '/') {
            throw new InvalidArgumentException("The targetDirectory: '$targetDirectory' does not end in '/'");
        }
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof FileNodeCollectionInterface)) {
            throw new InvalidArgumentException("Node: $node should be an instance of FileNodeCollectionInterface");
        }

        $each = new Each(new MoveFile($this->targetDirectory));
        return $each->flow($node);
    }
}
