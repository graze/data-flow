<?php

namespace Graze\DataFlow\Test\Integration\Flow\File;

use Graze\DataFile\Node\LocalFile;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Flow\File\ReplaceText;
use Graze\DataFlow\Test\RealFileTestCase;
use Mockery as m;

class ReplaceTextTest extends RealFileTestCase
{
    public function testReplaceText()
    {
        $file = $this->makeFile('replaceText/initial/from', 'some text');
        $flow = new ReplaceText('text', 'bananas');

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(['some bananas'], $output->getContents());
    }

    public function testStaticFlow()
    {
        $file = $this->makeFile('replaceText/static/from', 'some text');
        $flow = Flow::replaceText('text', 'bananas');

        $output = $flow->flow($file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(['some bananas'], $output->getContents());
    }

    public function testInvokeFlow()
    {
        $file = $this->makeFile('replaceText/invoke/from', 'some text');
        $flow = new ReplaceText('text', 'bananas');

        $output = call_user_func($flow, $file);

        static::assertNotSame($file, $output);
        static::assertInstanceOf(LocalFile::class, $output);
        static::assertEquals(['some bananas'], $output->getContents());
    }
}
