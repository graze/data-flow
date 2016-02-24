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

namespace Graze\DataFlow;

use Graze\DataNode\NodeInterface;

interface FlowInterface
{
    /**
     * Run a 'flow' through the handler
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function flow(NodeInterface $node);
}
