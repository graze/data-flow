<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\DeCompress;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class DeCompressTest extends RealFileTestCase
{
    public function testDeCompressNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new DeCompress();

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testDeCompress()
    {
        $file = $this->makeFile('decompress/initial/source', 'some text');
        $compressed = Flow::compress(Gzip::NAME)->flow($file);
        $flow = new DeCompress();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('decompress/static/source', 'some text');
        $compressed = Flow::compress(Gzip::NAME)->flow($file);
        $flow = Flow::decompress();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('decompress/invoke/source', 'some text');
        $compressed = Flow::compress(Gzip::NAME)->flow($file);
        $flow = new DeCompress();

        $output = call_user_func($flow, $compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }
}
