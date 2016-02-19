<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Node\FileNodeCollection;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\Merge;
use Graze\DataFlow\Test\RealFileTestCase;
use Mockery as m;

class MergeTest extends RealFileTestCase
{
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
