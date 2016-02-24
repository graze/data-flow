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

use ArrayIterator;
use Graze\DataFlow\FlowInterface;
use IteratorAggregate;
use Serializable;

class FlowCollection implements IteratorAggregate, Serializable
{
    /**
     * @var FlowInterface[]
     */
    protected $items = [];

    /**
     * FlowCollection constructor.
     *
     * @param FlowInterface ...$flows
     */
    public function __construct(...$flows)
    {
        $this->addFlows($flows);
    }

    /**
     * @param FlowInterface[] $items
     *
     * @return $this
     */
    public function addFlows(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * @return FlowInterface[]
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * @param FlowInterface $flow
     *
     * @return static
     */
    public function add(FlowInterface $flow)
    {
        $this->items[] = $flow;

        return $this;
    }

    /**
     * @param FlowInterface $flow
     *
     * @return static
     */
    public function remove(FlowInterface $flow)
    {
        $index = array_search($flow, $this->items, true);
        if ($index !== false) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }

        return $this;
    }

    /**
     * @param FlowInterface $flow
     *
     * @return bool
     */
    public function contains(FlowInterface $flow)
    {
        return in_array($flow, $this->items, true);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $this->items = unserialize($data);
    }
}
