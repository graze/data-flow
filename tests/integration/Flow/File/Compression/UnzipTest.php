<?php
/**
 * This file is part of graze/data-flow
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/data-flow/blob/master/LICENSE.md
 * @link    https://github.com/graze/data-flow
 */

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\Zip;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Unzip;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class UnzipTest extends RealFileTestCase
{
    public function testCompressNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Unzip();

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testDeCompress()
    {
        $file = $this->makeFile('unzip/initial/source.txt', 'some text');
        $compressed = Flow::compress(Zip::NAME)->flow($file);
        $flow = new Unzip();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('unzip/static/source.txt', 'some text');
        $compressed = Flow::compress(Zip::NAME)->flow($file);
        $flow = Flow::unzip();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('unzip/invoke/source.txt', 'some text');
        $compressed = Flow::compress(Zip::NAME)->flow($file);
        $flow = new Unzip();

        $output = call_user_func($flow, $compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }
}
