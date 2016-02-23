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

use Graze\DataFile\Node\FileNodeCollection;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\CopyFiles;
use Graze\DataFlow\Test\MemoryFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class CopyFilesTest extends MemoryFileTestCase
{
    public function testCopyFilesNotOnLocalFileWillThrowAnException()
    {
        $target = $this->makeFile('copys/flow/target/');
        $flow = new CopyFiles($target);

        $this->expectException(InvalidArgumentException::class);

        $flow->flow(m::mock(NodeInterface::class));
    }

    public function testCopyFilesWithATargetThatIsNotADirectoryWillThrowAnException()
    {
        $target = $this->makeFile('copys/flow/target');

        $this->expectException(InvalidArgumentException::class);

        new CopyFiles($target);
    }

    public function testCopyFilesCreatesANewFile()
    {
        $file = $this->makeFile('copys/flow/source/file', 'some text');
        $target = $this->makeFile('copys/flow/target/');

        $flow = new CopyFiles($target);

        $output = $flow->flow(new FileNodeCollection([$file]));

        static::assertInstanceOf(FileNodeCollection::class, $output);
        static::assertEquals(1, $output->count());
        static::assertEquals('copys/flow/target/file', $output->getAll()[0]->getPath());
        static::assertTrue($file->exists(), "The original file should still exist");
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('copys/static/source/file', 'some text');
        $target = $this->makeFile('copys/static/target/');

        $flow = Flow::copyFiles($target);

        $output = $flow->flow(new FileNodeCollection([$file]));

        static::assertInstanceOf(FileNodeCollection::class, $output);
        static::assertEquals(1, $output->count());
        static::assertEquals('copys/static/target/file', $output->getAll()[0]->getPath());
        static::assertTrue($file->exists(), "The original file should still exist");
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('copys/invoke/source/file', 'some text');
        $target = $this->makeFile('copys/invoke/target/');

        $flow = Flow::copyFiles($target);

        $output = call_user_func($flow, new FileNodeCollection([$file]));

        static::assertInstanceOf(FileNodeCollection::class, $output);
        static::assertEquals(1, $output->count());
        static::assertEquals('copys/invoke/target/file', $output->getAll()[0]->getPath());
        static::assertTrue($file->exists(), "The original file should still exist");
    }
}
