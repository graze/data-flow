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

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFlow\FlowInterface;
use Psr\Log\LoggerAwareInterface;

abstract class AbstractFlow implements LoggerAwareInterface, FlowInterface
{
    use InvokeTrait;
    use OptionalLoggerTrait;
}
