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

use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow\InvokeTrait;
use Graze\DataFlow\FlowInterface;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;

class ReplaceText extends \Graze\DataFile\Modify\ReplaceText implements FlowInterface
{
    use InvokeTrait;
    /**
     * @var
     */
    private $from;
    /**
     * @var
     */
    private $to;

    /**
     * ReplaceText constructor.
     *
     * @param string|string[] $from The text to be replaced
     * @param string|string[] $to   The new text
     * @param array           $options
     */
    public function __construct($from, $to, array $options = [])
    {
        $this->from = $from;
        $this->to = $to;
        $this->options = $options;
    }

    /**
     * Run a 'flow' through the handler
     *
     * @param NodeInterface $node
     *
     * @return LocalFile
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof LocalFile)) {
            throw new InvalidArgumentException("Node: $node should be an instance of LocalFile");
        }

        return $this->replaceText($node, $this->from, $this->to, $this->options);
    }
}
