<?php

namespace Graze\DataFlow\Test\Unit\Node\File;

use Graze\DataFlow\Node\File\FileNodeCollection;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

class FileNodeCollectionTest extends TestCase
{
    /**
     * @var FileNodeCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new FileNodeCollection();
    }

    public function testIsDataNodeCollection()
    {
        static::assertInstanceOf('Graze\DataFlow\Node\DataNodeCollection', $this->collection);
    }

    public function testGetCommonPrefixReturnsCommonPrefixOfFiles()
    {
        $file1 = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file1->shouldReceive('getPath')->andReturn('some/common/path/to/file1.txt');
        $file2 = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file2->shouldReceive('getPath')->andReturn('some/common/path/to/file2.txt');
        $file3 = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file3->shouldReceive('getPath')->andReturn('some/common/path/to/file3.txt');

        $this->collection->add($file1);
        $this->collection->add($file2);
        $this->collection->add($file3);

        static::assertEquals('some/common/path/to/file', $this->collection->getCommonPrefix());
    }

    public function testGetCommonPrefixReturnsNullIfThereIsNoCommonPrefix()
    {
        $file1 = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file1->shouldReceive('getPath')->andReturn('some/common/path/to/file1.txt');
        $file2 = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file2->shouldReceive('getPath')->andReturn('some/common/path/to/file2.txt');
        $file3 = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file3->shouldReceive('getPath')->andReturn('other/nonCommon/path/to/file3.txt');

        $this->collection->add($file1);
        $this->collection->add($file2);
        $this->collection->add($file3);

        static::assertNull($this->collection->getCommonPrefix());
    }

    public function testGetCommonPrefixReturnsNullIfThereAreNoItems()
    {
        static::assertNull($this->collection->getCommonPrefix());
    }

    public function testCanAddAFileNode()
    {
        $node = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        static::assertSame($this->collection, $this->collection->add($node));
    }

    public function testAddingANonDataNodeWillThrowAnException()
    {
        $node = m::mock('Graze\DataFlow\Node\DataNodeInterface');

        static::setExpectedException(
            'InvalidArgumentException',
            "The specified value does not implement FileNodeInterface"
        );

        $this->collection->add($node);
    }
}
