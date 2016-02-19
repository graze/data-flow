<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Compress;
use Graze\DataFlow\Test\RealFileTestCase;
use Mockery as m;

class CompressTest extends RealFileTestCase
{
    public function testCompress()
    {
        $file = $this->makeFile('compress/initial/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = new Compress(CompressionType::GZIP);

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::GZIP, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('compress/static/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = Flow::compress(CompressionType::GZIP);

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::GZIP, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('compress/invoke/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = new Compress(CompressionType::GZIP);

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::GZIP, $output->getCompression());
    }
}
