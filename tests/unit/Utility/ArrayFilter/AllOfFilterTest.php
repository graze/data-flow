<?php

namespace Graze\DataFlow\Test\Unit\Node\File\MetadataFilter;

use Graze\DataFlow\Test\TestCase;
use Graze\DataFlow\Utility\ArrayFilter\AllOfFilter;
use Graze\DataFlow\Utility\ArrayFilter\ClosureFilter;

class AllOfFilterTest extends TestCase
{
    public function testInstanceOf()
    {
        $filter = new AllOfFilter();
        static::assertInstanceOf('Graze\DataFlow\Utility\ArrayFilter\ArrayFilterInterface', $filter);
    }

    public function testSingleChildConstructorEquals()
    {
        $filter = new AllOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            })]);
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testSingleChildAddedLaterEquals()
    {
        $filter = new AllOfFilter();
        $filter->addFilter(new ClosureFilter('test', function ($actual) {
            return $actual == 'value';
        }));
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'values']));
    }

    public function testMultipleChildrenWithDifferentProperties()
    {
        $filter = new AllOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual == 'value';
            }),
            new ClosureFilter('test2', function ($actual) {
                return $actual == 'value2';
            }),
        ]);
        static::assertTrue($filter->matches(['test' => 'value', 'test2' => 'value2']));
        static::assertFalse($filter->matches(['test' => 'values', 'test2' => 'value2']));
    }

    public function testMultipleChildrenWithTheSameProperty()
    {
        $filter = new AllOfFilter([
            new ClosureFilter('test', function ($actual) {
                return $actual != 'foo';
            }),
            new ClosureFilter('test', function ($actual) {
                return $actual != 'bar';
            }),
        ]);
        static::assertTrue($filter->matches(['test' => 'value']));
        static::assertFalse($filter->matches(['test' => 'foo']));
        static::assertFalse($filter->matches(['test' => 'bar']));
    }
}
