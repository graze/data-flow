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

namespace Graze\DataFlow\Flow\Runner;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFlow\Flow\FlowCollection;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Runs through a set of flows but providing the initial node to all flows and return th
 */
class ToAll extends FlowCollection implements FlowInterface, LoggerAwareInterface
{
    use InvokeTrait;
    use OptionalLoggerTrait;

    /**
     * @inheritdoc
     */
    public function flow(NodeInterface $node)
    {
        $collection = new NodeCollection();
        foreach ($this->items as $flow) {
            $collection->add($flow->flow($node));
        }
        return $collection;
    }
}
