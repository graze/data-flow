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
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Merge;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeCollectionInterface;
use InvalidArgumentException;
use Mockery as m;

class MergeTest extends RealFileTestCase
{
    public function testHeadNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeCollectionInterface::class);
        $flow = new Merge(m::mock(FileNodeInterface::class));

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testMakeDirectory()
    {
        $file1 = $this->makeFile('merge/initial/file1', "line 1\nline 2\n");
        $file2 = $this->makeFile('merge/initial/file2', "line 3\nline 4\n");
        $merged = $this->makeFile('merge/initial/merged');

        $flow = new Merge($merged);
        $collection = new FileNodeCollection([$file1, $file2]);

        $output = $flow->flow($collection);

        static::assertSame($output, $merged);
        static::assertEquals(["line 1", "line 2", "line 3", "line 4"], $output->getContents());
    }

    public function testStaticFlow()
    {
        $file1 = $this->makeFile('merge/static/file1', "line 1\nline 2\n");
        $file2 = $this->makeFile('merge/static/file2', "line 3\nline 4\n");
        $merged = $this->makeFile('merge/static/merged');

        $flow = Flow::merge($merged);
        $collection = new FileNodeCollection([$file1, $file2]);

        $output = $flow->flow($collection);

        static::assertSame($output, $merged);
        static::assertEquals(["line 1", "line 2", "line 3", "line 4"], $output->getContents());
    }

    public function testInvokeFlow()
    {
        $file1 = $this->makeFile('merge/invoke/file1', "line 1\nline 2\n");
        $file2 = $this->makeFile('merge/invoke/file2', "line 3\nline 4\n");
        $merged = $this->makeFile('merge/invoke/merged');

        $flow = new Merge($merged);
        $collection = new FileNodeCollection([$file1, $file2]);

        $output = call_user_func($flow, $collection);

        static::assertSame($output, $merged);
        static::assertEquals(["line 1", "line 2", "line 3", "line 4"], $output->getContents());
    }
}
