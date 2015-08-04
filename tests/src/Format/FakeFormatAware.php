<?php

namespace Graze\DataFlow\Test\Format;

use Graze\DataFlow\Format\FormatAwareInterface;
use Graze\DataFlow\Format\FormatAwareTrait;

class FakeFormatAware implements FormatAwareInterface
{
    use FormatAwareTrait;

    public function __clone()
    {
        if ($this->format) {
            $this->format = clone $this->format;
        }
    }
}
