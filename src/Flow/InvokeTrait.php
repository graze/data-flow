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

namespace Graze\DataFlow\Flow;

use Graze\DataNode\NodeInterface;

trait InvokeTrait
{
    /**
     * Invoke this Flow
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function __invoke(NodeInterface $node)
    {
        return $this->flow($node);
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    abstract public function flow(NodeInterface $node);
}
