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
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Compression\Gzip;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class GzipTest extends RealFileTestCase
{
    public function testCompressNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Gzip();

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testCompress()
    {
        $file = $this->makeFile('gzip/initial/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = new Gzip();

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Gzip::NAME, $output->getCompression());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('gzip/static/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = Flow::gzip();

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Gzip::NAME, $output->getCompression());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('gzip/invoke/source', 'some text');
        $file->setCompression(CompressionFactory::TYPE_NONE);
        $flow = new Gzip();

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(Gzip::NAME, $output->getCompression());
    }
}
