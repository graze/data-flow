<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Node\FileNode;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\CopyFile;
use Graze\DataFlow\Test\MemoryFileTestCase;

class CopyMemoryFileTest extends MemoryFileTestCase
{
    public function testCopyFileCreatesANewFile()
    {
        $file = $this->makeFile('copy/flow/source', 'some text');
        $target = $this->makeFile('copy/flow/target');

        $flow = new CopyFile($target);

        $output = $flow->flow($file);

        static::assertSame($target, $output);
        static::assertInstanceOf(FileNode::class, $output);
        static::assertEquals($file->getContents(), $output->getContents());
        static::assertTrue($file->exists(), "The original file should still exist");
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('copy/static/source', 'some text');
        $target = $this->makeFile('copy/static/target');

        $flow = Flow::copyFile($target);

        $output = $flow->flow($file);

        static::assertSame($target, $output);
        static::assertInstanceOf(FileNode::class, $output);
        static::assertEquals($file->getContents(), $output->getContents());
        static::assertTrue($file->exists(), "The original file should still exist");
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('copy/invoke/source', 'some text');
        $target = $this->makeFile('copy/invoke/target');

        $flow = Flow::copyFile($target);

        $output = call_user_func($flow, $file);

        static::assertSame($target, $output);
        static::assertInstanceOf(FileNode::class, $output);
        static::assertEquals($file->getContents(), $output->getContents());
        static::assertTrue($file->exists(), "The original file should still exist");
    }

    public function testCopyFileWithDirectoryTargetWillUseOriginalFileName()
    {
        $file = $this->makeFile('copy/todir/source', 'some text');
        $target = $this->makeFile('copy/todir/target/');

        $flow = Flow::copyFile($target);

        $output = $flow->flow($file);

        static::assertNotSame($target, $output);
        static::assertInstanceOf(FileNode::class, $output);
        static::assertEquals($file->getContents(), $output->getContents());
        static::assertEquals('source', $output->getFilename());
        static::assertEquals('copy/todir/target/source', $output->getPath());
    }
}
