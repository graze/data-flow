<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Zip;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class ZipTest extends RealFileTestCase
{
    public function testCompressNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Zip();

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testCompress()
    {
        $file = $this->makeFile('zip/initial/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = new Zip();

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Zip::NAME, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('zip/static/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = Flow::zip();

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Zip::NAME, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('zip/invoke/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = new Zip();

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Zip::NAME, $output->getCompression());
    }
}
