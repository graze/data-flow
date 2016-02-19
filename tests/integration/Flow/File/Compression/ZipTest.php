<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Zip;
use Graze\DataFlow\Test\RealFileTestCase;
use Mockery as m;

class ZipTest extends RealFileTestCase
{
    public function testCompress()
    {
        $file = $this->makeFile('zip/initial/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = new Zip();

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::ZIP, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('zip/static/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = Flow::zip();

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::ZIP, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('zip/invoke/source', 'some text');
        $file->setCompression(CompressionType::NONE);
        $flow = new Zip();

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionType::ZIP, $output->getCompression());
    }
}
