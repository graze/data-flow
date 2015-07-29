<?php

namespace Graze\DataFlow\Utility;

trait GetOption
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get an option value
     *
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    protected function getOption($name, $default)
    {
        return (isset($this->options[$name])) ? $this->options[$name] : $default;
    }
}
