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

namespace Graze\DataFlow\Flow\Collection;

use Graze\DataFlow\Flow\AbstractFlow;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

/**
 * Apply a flow to each element of a NodeCollection
 */
class Each extends AbstractFlow
{
    /**
     * @var FlowInterface
     */
    private $flow;

    /**
     * Each constructor.
     *
     * @param FlowInterface $flow
     */
    public function __construct(FlowInterface $flow)
    {
        $this->flow = $flow;
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeCollectionInterface
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof NodeCollectionInterface)) {
            throw new InvalidArgumentException("The supplied $node is not a NodeCollectionInterface");
        }

        $map = new Map(function (NodeInterface $item) {
            return $this->flow->flow($item);
        });
        return $map->flow($node);
    }
}
