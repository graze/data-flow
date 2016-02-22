<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\ConvertEncoding;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class ConvertEncodingTest extends RealFileTestCase
{
    public function testConvertEncodingNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new ConvertEncoding('UTF-16');

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testChangeEncoding()
    {
        $file = $this->makeFile('encoding.utf8', mb_convert_encoding('some#¢±±§', 'UTF-8'));
        $file->setEncoding('UTF-8');
        $flow = new ConvertEncoding('UTF-16');

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals('UTF-16', $output->getEncoding());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('encoding.static.utf8', mb_convert_encoding('some#¢±±§', 'UTF-8'));
        $file->setEncoding('UTF-8');
        $flow = Flow::convertEncoding('UTF-16');

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals('UTF-16', $output->getEncoding());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('encoding.invoke.utf8', mb_convert_encoding('some#¢±±§', 'UTF-8'));
        $file->setEncoding('UTF-8');
        $flow = Flow::convertEncoding('UTF-16');

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals('UTF-16', $output->getEncoding());
    }
}
