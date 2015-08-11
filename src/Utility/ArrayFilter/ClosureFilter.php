<?php

namespace Graze\DataFlow\Utility\ArrayFilter;

use Closure;

class ClosureFilter implements ArrayFilterInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var Closure
     */
    private $function;

    /**
     * @param string $property
     * @param Closure $function ($value) -> bool
     */
    public function __construct($property, Closure $function)
    {
        $this->property = $property;
        $this->function = $function;
    }

    /**
     * Does this filter match?
     *
     * @param array $metadata File metadata
     *                        array(:type,:path,:timestamp[,:size],:dirname,:basename[,:filename,:extension])
     * @return bool
     */
    public function matches($metadata)
    {
        return (isset($metadata[$this->property]) &&
            call_user_func($this->function, $metadata[$this->property]));
    }
}
