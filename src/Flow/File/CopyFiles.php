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

namespace Graze\DataFlow\Flow\File;

use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFlow\Flow\AbstractFlow;
use Graze\DataFlow\Flow\Collection\Each;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

class CopyFiles extends AbstractFlow
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

        $each = new Each(new CopyFile($this->targetDirectory));
        return $each->flow($node);
    }
}
