<?php

namespace Graze\DataFlow\Utility\ArrayFilter;

use Closure;
use DateTime;

class ValueFactory implements ValueFactoryInterface
{
    /**
     * @var array List of mapping templates to apply '<regex>' => 'value/closure'
     */
    private $mappings = [];

    public function __construct()
    {
        $this->mappings = [
            '/(?<!:\{)\{date:([^\}:]+):?([^\}]+)?\}(?!:\})/i' => function ($matches) {
                $dt     = new DateTime($matches[1]);
                $format = isset($matches[2]) ? $matches[2] : 'c';
                return $dt->format($format);
            },
        ];
    }

    /**
     * @param string         $regex
     * @param string|Closure $replace
     *
     * @return $this
     */
    public function addMapping($regex, $replace)
    {
        $this->mappings[$regex] = $replace;
    }

    /**
     * Parse the supplied value and return the interpreted value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        foreach ($this->mappings as $mapping => $replace) {
            if (is_callable($replace)) {
                $value = preg_replace_callback($mapping, $replace, $value);
            } else {
                $value = preg_replace($mapping, $replace, $value);
            }
        }

        return $value;
    }
}
