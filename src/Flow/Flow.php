<?php

namespace Graze\DataFlow\Flow;

abstract class Flow
{
    /**
     * Call to ensure this class is loaded on the stack for (semi) auto discovery to work
     */
    public static function aware()
    {
    }
}
