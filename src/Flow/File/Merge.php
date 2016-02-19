<?php

namespace Graze\DataFlow\Flow\File;

use Graze\DataFile\Modify\Contract\MergeFiles;
use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;

class Merge extends MergeFiles implements FlowInterface
{
    use InvokeTrait;

    /**
     * @var FileNodeInterface
     */
    private $file;

    /**
     * @param FileNodeInterface $file
     * @param array             $options
     */
    public function __construct(
        FileNodeInterface $file,
        array $options = []
    ) {
        $this->file = $file;
        $this->options = $options;
    }

    /**
     * Add files from a local Directory
     *
     * @param NodeInterface $node
     *
     * @return FileNodeInterface
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof FileNodeCollectionInterface)) {
            throw new \InvalidArgumentException("The supplied $node is not a FileNodeCollectionInterface");
        }

        return $this->contract($node, $this->file, $this->options);
    }
}
