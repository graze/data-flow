<?php

namespace Graze\DataFlow\Test\Unit\Node;

use Graze\DataFlow\Node\DataNodeCollection;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

class DataNodeCollectionTest extends TestCase
{
    /**
     * @var DataNodeCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new DataNodeCollection();
    }

    public function testIsExtensible()
    {
        static::assertInstanceOf('Graze\Extensible\ExtensibleInterface', $this->collection);
    }

    public function testIsCollection()
    {
        static::assertInstanceOf('Graze\DataStructure\Collection\Collection', $this->collection);
    }

    public function testIsAutoExtensible()
    {
        static::assertInstanceOf('Graze\Extensible\Finder\DocBlockExtensionFinder', $this->collection->getFinder());
    }

    public function testCanAddADataNode()
    {
        $node = m::mock('Graze\DataFlow\Node\DataNodeInterface');
        static::assertSame($this->collection, $this->collection->add($node));
    }

    public function testAddingANonDataNodeWillThrowAnException()
    {
        $node = m::mock('Graze\Extensible\ExtensibleInterface');

        static::setExpectedException(
            'InvalidArgumentException',
            "The specified value does not implement DataNodeInterface"
        );

        $this->collection->add($node);
    }

    public function testCallingApplyWillModifyTheContentsUsingReference()
    {
        $node = m::mock('Graze\DataFlow\Node\DataNodeInterface');
        $node->shouldReceive('someMethod')
            ->once()
            ->andReturn(null);

        $this->collection->add($node);

        $this->collection->apply(function (&$item) {
            $item->someMethod();
        });

        $item = $this->collection->getAll()[0];
        static::assertSame($node, $item);
    }

    public function testCallingApplyWillModifyTheContentsUsingReturnValue()
    {
        $node = m::mock('Graze\DataFlow\Node\DataNodeInterface');
        $node->shouldReceive('someMethod')
             ->once()
             ->andReturn(null);

        $this->collection->add($node);

        $this->collection->apply(function ($item) {
            $item->someMethod();
            return $item;
        });

        $item = $this->collection->getAll()[0];
        static::assertSame($node, $item);
    }
}
