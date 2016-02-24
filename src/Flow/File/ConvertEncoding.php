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

/**
 * Convert the Encoding of a file
 *
 * For a list of the supported encodings run:
 *
 * ```bash
 * iconv -l
 * ```
 */
class ConvertEncoding extends \Graze\DataFile\Modify\Encoding\ConvertEncoding implements FlowInterface
{
    use InvokeTrait;

    /**
     * @var string
     */
    private $encoding;

    /**
     * @param string $encoding Encoding as defined by iconv
     * @param array  $options  -postfix <string> (Default: toEncoding)
     *                         -keepOldFile <bool> (Default: true)
     */
    public function __construct($encoding, array $options = [])
    {
        $this->encoding = $encoding;
        $this->options = $options;
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface
     * @throws InvalidArgumentException
     */
    public function flow(NodeInterface $node)
    {
        if (!($node instanceof LocalFile)) {
            throw new InvalidArgumentException("Node: $node should be an instance of LocalFile");
        }

        return $this->toEncoding($node, $this->encoding, $this->options);
    }
}
