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
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Gunzip;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class GunzipTest extends RealFileTestCase
{
    public function testDeCompressNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Gunzip();

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testDeCompress()
    {
        $file = $this->makeFile('gunzip/initial/source', 'some text');
        $compressed = Flow::compress(Gzip::NAME)->flow($file);
        $flow = new Gunzip();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('gunzip/static/source', 'some text');
        $compressed = Flow::compress(Gzip::NAME)->flow($file);
        $flow = Flow::gunzip();

        $output = $flow->flow($compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('gunzip/invoke/source', 'some text');
        $compressed = Flow::compress(Gzip::NAME)->flow($file);
        $flow = new Gunzip();

        $output = call_user_func($flow, $compressed);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(CompressionFactory::TYPE_NONE, $output->getCompression());
    }
}
