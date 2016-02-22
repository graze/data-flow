<?php

namespace Graze\DataFlow\Flow\File;

use Graze\DataFile\Modify\Exception\MakeDirectoryFailedException;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;

class MakeDirectory extends \Graze\DataFile\Modify\MakeDirectory implements FlowInterface
{
    use InvokeTrait;

    /**
     * @var string|null
     */
    private $visibility;

    /**
     * MakeDirectory constructor.
     *
     * @param null $visibility
     */
    public function __construct($visibility = null)
    {
        $this->visibility = $visibility;
    }

    /**
     * Create the directory specified by the $file if it does not exist
     *
     * @param NodeInterface $node
     *
     * @return LocalFile The original file inputted
     * @throws MakeDirectoryFailedException
     *
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof LocalFile)) {
            throw new \InvalidArgumentException("Node: $node is not a LocalFile");
        }

        return $this->makeDirectory($node, $this->visibility);
    }
}
