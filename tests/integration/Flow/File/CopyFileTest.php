<?php
/**
 * This file is part of graze/data-flow
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/data-flow/blob/master/LICENSE.md
 * @link    https://github.com/graze/data-flow
 */

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Node\FileNode;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\CopyFile;
use Graze\DataFlow\Test\MemoryFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class CopyFileTest extends MemoryFileTestCase
{
    public function testCopyNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new CopyFile(m::mock(FileNode::class));

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

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
