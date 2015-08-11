<?php

namespace Graze\DataFlow\Utility\ArrayFilter;

use Graze\DataFlow\Utility\ArrayFilter\Exception\UnknownPropertyDefinitionException;

/**
 * Class ArrayFilterFactory
 *
 * Factory class to interpret string filter configuration into different configurations
 *
 * Currently supports:
 *
 * ```
 * [
 *     'param' => 'value',    // $metadata['param'] == 'value';
 *     'param =' => 'value',  // $metadata['param'] == 'value';
 *     'param ~' => 'value',  // preg_match($value, $metadata['param']);
 *     'param >' => 'value',  // $metadata['param'] > 'value';
 *     'param >=' => 'value', // $metadata['param'] >= 'value';
 *     'param <' => 'value',  // $metadata['param'] < 'value';
 *     'param <=' => 'value', // $metadata['param'] <= 'value';
 *     'param !=' => 'value', // $metadata['param'] != 'value';
 *     'param <>' => 'value', // $metadata['param'] != 'value';
 *     'param in' => ['value1','value2'], // in_array($metadata['param'], ['value1','value2'])
 * ]
 * ```
 *
 * @package Graze\DataFlow\Node\File\MetadataFilter
 */
class ArrayFilterFactory implements ArrayFilterFactoryInterface
{
    /**
     * @var array
     */
    protected $definitions;

    /**
     * Build the definitions
     */
    public function __construct()
    {
        $this->definitions = [
            '/^(\w+)(\s*=)?$/i'    => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual == $expected;
                }
                );
            },
            '/^(\w+)\s*~=?$/i'     => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return preg_match($expected, $actual);
                }
                );
            },
            '/^(\w+)\s*>$/i'       => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual > $expected;
                }
                );
            },
            '/^(\w+)\s*>=$/i'      => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual >= $expected;
                }
                );
            },
            '/^(\w+)\s*<$/i'       => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual < $expected;
                }
                );
            },
            '/^(\w+)\s*<=$/i'      => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual <= $expected;
                }
                );
            },
            '/^(\w+)\s*(<>|!=)$/i' => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return $actual != $expected;
                }
                );
            },
            '/^(\w+)\s*in$/i' => function ($property, $expected) {
                return new ClosureFilter($property, function ($actual) use ($expected) {
                    return in_array($actual, $expected);
                }
                );
            },
        ];
    }

    /**
     * @param array $configuration
     * @return ArrayFilterInterface
     */
    public function createFilters(array $configuration)
    {
        $filters = [];
        foreach ($configuration as $property => $value) {
            $filters[] = $this->createFilter($property, $value);
        }
        return new AllOfFilter($filters);
    }

    /**
     * @param string $property
     * @param mixed  $value
     * @return ArrayFilterInterface
     * @throws UnknownPropertyDefinitionException
     */
    public function createFilter($property, $value)
    {
        foreach ($this->definitions as $key => $definition) {
            if (preg_match($key, $property, $matches)) {
                return call_user_func($definition, $matches[1], $value);
            }
        }

        throw new UnknownPropertyDefinitionException($property);
    }
}
