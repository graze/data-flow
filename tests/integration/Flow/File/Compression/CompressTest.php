<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Compress;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class CompressTest extends RealFileTestCase
{
    public function testCompressNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Compress(Gzip::NAME);

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testCompress()
    {
        $file = $this->makeFile('compress/initial/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = new Compress(Gzip::NAME);

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Gzip::NAME, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('compress/static/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = Flow::compress(Gzip::NAME);

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Gzip::NAME, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('compress/invoke/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = new Compress(Gzip::NAME);

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Gzip::NAME, $output->getCompression());
    }
}
