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

use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Tail;
use Graze\DataFlow\Test\RealFileTestCase;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class TailTest extends RealFileTestCase
{
    public function testTailNotOnLocalFileWillThrowAnException()
    {
        $file = m::mock(NodeInterface::class);
        $flow = new Tail(2);

        $this->expectException(InvalidArgumentException::class);

        $flow->flow($file);
    }

    public function testTail()
    {
        $file = $this->makeFile('tail/initial', "line 1\nline 2\nline 3");
        $flow = new Tail(1);

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(["line 3"], $output->getContents());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('tail/static', "line 1\nline 2\nline 3");
        $flow = Flow::tail(1);

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(["line 3"], $output->getContents());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('tail/invoke', "line 1\nline 2\nline 3");
        $flow = new Tail(1);

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(["line 3"], $output->getContents());
    }
}
