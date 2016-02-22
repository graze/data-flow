<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Node\FileNodeCollection;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\MoveFiles;
use Graze\DataFlow\Test\MemoryFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class MoveFilesTest extends MemoryFileTestCase
{
    public function testMoveFilesNotOnLocalFileWillThrowAnException()
    {
        $target = $this->makeFile('moves/flow/target/');
        $flow = new MoveFiles($target);

        $this->expectException(InvalidArgumentException::class);

        $flow->flow(m::mock(NodeInterface::class));
    }

    public function testMoveFilesWithATargetThatIsNotADirectoryWillThrowAnException()
    {
        $target = $this->makeFile('moves/flow/target');

        $this->expectException(InvalidArgumentException::class);

        new MoveFiles($target);
    }

    public function testMoveFilesCreatesANewFile()
    {
        $file = $this->makeFile('moves/flow/source/file', 'some text');
        $target = $this->makeFile('moves/flow/target/');

        $flow = new MoveFiles($target);

        $output = $flow->flow(new FileNodeCollection([$file]));

        static::assertInstanceOf(FileNodeCollection::class, $output);
        static::assertEquals(1, $output->count());
        static::assertEquals('moves/flow/target/file', $output->getAll()[0]->getPath());
        static::assertFalse($file->exists(), "The original file should not exist");
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('moves/static/source/file', 'some text');
        $target = $this->makeFile('moves/static/target/');

        $flow = Flow::moveFiles($target);

        $output = $flow->flow(new FileNodeCollection([$file]));

        static::assertInstanceOf(FileNodeCollection::class, $output);
        static::assertEquals(1, $output->count());
        static::assertEquals('moves/static/target/file', $output->getAll()[0]->getPath());
        static::assertFalse($file->exists(), "The original file should not exist");
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('moves/invoke/source/file', 'some text');
        $target = $this->makeFile('moves/invoke/target/');

        $flow = Flow::moveFiles($target);

        $output = call_user_func($flow, new FileNodeCollection([$file]));

        static::assertInstanceOf(FileNodeCollection::class, $output);
        static::assertEquals(1, $output->count());
        static::assertEquals('moves/invoke/target/file', $output->getAll()[0]->getPath());
        static::assertFalse($file->exists(), "The original file should not exist");
    }
}
