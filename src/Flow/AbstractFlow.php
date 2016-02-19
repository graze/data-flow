<?php

namespace Graze\DataFlow\Flow;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFlow\FlowInterface;
use Psr\Log\LoggerAwareInterface;

abstract class AbstractFlow implements LoggerAwareInterface, FlowInterface
{
    use InvokeTrait;
    use OptionalLoggerTrait;
}
