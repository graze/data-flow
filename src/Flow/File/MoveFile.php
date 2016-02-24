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

use Graze\DataFile\Modify\Transfer\Transfer;
use Graze\DataFile\Node\FileNode;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

class MoveFile extends Transfer implements FlowInterface
{
    use InvokeTrait;

    /**
     * @var FileNode
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

        return $this->moveTo($node, $target);
    }
}
