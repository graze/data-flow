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

use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\MakeDirectory;
use Graze\DataFlow\Test\RealFileTestCase;
use InvalidArgumentException;
use Mockery as m;

class MakeDirectoryTest extends RealFileTestCase
{
    public function testMakeDirectoryNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(FileNodeInterface::class);
        $flow = new MakeDirectory();

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testMakeDirectory()
    {
        $file = $this->makeFile('makeDirectory/initial/file');
        $flow = new MakeDirectory();

        static::assertFalse(is_dir($file->getDirectory()));
        $output = $flow->flow($file);
        static::assertTrue(is_dir($output->getDirectory()));
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('makeDirectory/static/file');
        $flow = Flow::makeDirectory();

        static::assertFalse(is_dir($file->getDirectory()));
        $output = $flow->flow($file);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertTrue(is_dir($output->getDirectory()));
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('makeDirectory/invoke/file');
        $flow = new MakeDirectory();

        static::assertFalse(is_dir($file->getDirectory()));
        $output = call_user_func($flow, $file);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertTrue(is_dir($output->getDirectory()));
    }
}
