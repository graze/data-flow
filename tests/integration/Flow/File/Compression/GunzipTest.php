<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Gunzip;
use Graze\DataFlow\Test\RealFileTestCase;
use Mockery as m;

class GunzipTest extends RealFileTestCase
{
    public function testDeCompress()
    {
        $file = $this->makeFile('gunzip/initial/source', 'some text');
        $compressed = Flow::compress(CompressionType::GZIP)->flow($file);
        $flow = new Gunzip();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::NONE, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('gunzip/static/source', 'some text');
        $compressed = Flow::compress(CompressionType::GZIP)->flow($file);
        $flow = Flow::gunzip();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::NONE, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('gunzip/invoke/source', 'some text');
        $compressed = Flow::compress(CompressionType::GZIP)->flow($file);
        $flow = new Gunzip();

        $output = call_user_func($flow, $compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::NONE, $output->getCompression());
    }
}
