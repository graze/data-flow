<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Gzip;
use Graze\DataFlow\Test\RealFileTestCase;
use Mockery as m;

class GzipTest extends RealFileTestCase
{
    public function testCompress()
    {
        $file = $this->makeFile('gzip/initial/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = new Gzip();

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::GZIP, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('gzip/static/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = Flow::gzip();

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::GZIP, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('gzip/invoke/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = new Gzip();

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::GZIP, $output->getCompression());
    }
}
