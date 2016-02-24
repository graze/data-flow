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
use Psr\Log\LogLevel;

class Callback extends AbstractFlow
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Callback constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function flow(NodeInterface $node)
    {
        $this->log(LogLevel::DEBUG, "Running callback flow");
        return call_user_func($this->callback, $node);
    }
}
