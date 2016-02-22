<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Head;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class HeadTest extends RealFileTestCase
{
    public function testHeadNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Head(2);

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testHead()
    {
        $file = $this->makeFile('head/initial', "line 1\nline 2\nline 3");
        $flow = new Head(1);

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(["line 1"], $output->getContents());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('head/static', "line 1\nline 2\nline 3");
        $flow = Flow::head(1);

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(["line 1"], $output->getContents());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('head/invoke', "line 1\nline 2\nline 3");
        $flow = new Head(1);

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(["line 1"], $output->getContents());
    }
}
