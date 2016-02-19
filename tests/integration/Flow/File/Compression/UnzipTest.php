<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Unzip;
use Graze\DataFlow\Test\RealFileTestCase;
use Mockery as m;

class UnzipTest extends RealFileTestCase
{
    public function testDeCompress()
    {
        $file = $this->makeFile('unzip/initial/source.txt', 'some text');
        $compressed = Flow::compress(CompressionType::ZIP)->flow($file);
        $flow = new Unzip();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::NONE, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('unzip/static/source.txt', 'some text');
        $compressed = Flow::compress(CompressionType::ZIP)->flow($file);
        $flow = Flow::unzip();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::NONE, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('unzip/invoke/source.txt', 'some text');
        $compressed = Flow::compress(CompressionType::ZIP)->flow($file);
        $flow = new Unzip();

        $output = call_user_func($flow, $compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::NONE, $output->getCompression());
    }
}
