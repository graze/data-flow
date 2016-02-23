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
use Graze\DataNode\NodeCollectionInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Psr\Log\LogLevel;

/**
 * Return the last element in a NodeCollection based on an option callback
 */
class Last extends AbstractFlow
{
    /**
     * @var callable|null
     */
    private $callback;

    /**
     * First constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback = null)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof NodeCollectionInterface)) {
            throw new InvalidArgumentException("The supplied $node is not a NodeCollectionInterface");
        }
        $this->log(LogLevel::DEBUG, "Selecting the last entry from {node}", ['node' => $node]);

        return $node->last($this->callback);
    }
}
