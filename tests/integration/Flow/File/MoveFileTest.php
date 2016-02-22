<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Node\FileNode;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\MoveFile;
use Graze\DataFlow\Test\MemoryFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class MoveMemoryFileTest extends MemoryFileTestCase
{
    public function testMoveFileNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new MoveFile(m::mock(FileNode::class));

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testCopyFileCreatesANewFile()
    {
        $file = $this->makeFile('move/flow/source', 'some text');
        $target = $this->makeFile('move/flow/target');

        $flow = new MoveFile($target);

        $output = $flow->flow($file);

        static::assertSame($target, $output);
        static::assertInstanceOf(FileNode::class, $output);
        static::assertEquals(['some text'], $output->getContents());
        static::assertFalse($file->exists(), "The original file should not exist");
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('move/static/source', 'some text');
        $target = $this->makeFile('move/static/target');

        $flow = Flow::moveFile($target);

        $output = $flow->flow($file);

        static::assertSame($target, $output);
        static::assertInstanceOf(FileNode::class, $output);
        static::assertEquals(['some text'], $output->getContents());
        static::assertFalse($file->exists(), "The original file should still exist");
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('move/invoke/source', 'some text');
        $target = $this->makeFile('move/invoke/target');

        $flow = Flow::moveFile($target);

        $output = call_user_func($flow, $file);

        static::assertSame($target, $output);
        static::assertInstanceOf(FileNode::class, $output);
        static::assertEquals(['some text'], $output->getContents());
        static::assertFalse($file->exists(), "The original file should still exist");
    }

    public function testCopyFileWithDirectoryTargetWillUseOriginalFileName()
    {
        $file = $this->makeFile('move/todir/source', 'some text');
        $target = $this->makeFile('move/todir/target/');

        $flow = Flow::copyFile($target);

        $output = $flow->flow($file);

        static::assertNotSame($target, $output);
        static::assertInstanceOf(FileNode::class, $output);
        static::assertEquals($file->getContents(), $output->getContents());
        static::assertEquals('source', $output->getFilename());
        static::assertEquals('move/todir/target/source', $output->getPath());
    }
}
