<?php

namespace Graze\DataFlow\Test\Unit\Utility\ArrayFilter;

use Graze\DataFlow\Test\TestCase;
use Graze\DataFlow\Utility\ArrayFilter\ClosureFilter;

class ClosureFilterTest extends TestCase
{
    public function testInstanceOf()
    {
        $filter = new ClosureFilter('', function ($actual) {
            return $actual == 'value';
        });
        static::assertInstanceOf('Graze\DataFlow\Utility\ArrayFilter\ArrayFilterInterface', $filter);
    }

    public function testBasicEquals()
    {
        $filter = new ClosureFilter('test', function ($actual) {
            return $actual == 'value';
        });
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testReturnsFalseWhenInvalidPropertySpecified()
    {
        $filter = new ClosureFilter('invalid', function ($actual) {
            return $actual == 'value';
        });
        static::assertFalse($filter->matches(['invilad' => 'value']));
    }
}
