<?php

namespace Graze\DataFlow\Format;

interface FormatInterface
{
    /**
     * Type type of file format
     *
     * @return string
     */
    public function getType();
}
