<?php

namespace Graze\DataFlow\Test\Fuctional\Flow\File;

use Graze\DataFlow\Flow\File\ReplaceText;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

class ReplaceTextTest extends FileTestCase
{
    /**
     * @var ReplaceText
     */
    protected $replacer;

    public function setUp()
    {
        $this->replacer = new ReplaceText();
    }

    public function testCanExtendAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->replacer->canExtend($localFile, 'replaceText'));
        static::assertFalse(
            $this->replacer->canExtend($localFile, 'replaceText'),
            "CanFlow should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->replacer->canExtend($randomThing, 'replaceText'));
    }

    public function testCanExtendOnlyAcceptsTheChangeEncodingMethod()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true);

        static::assertTrue($this->replacer->canExtend($localFile, 'replaceText'));
        static::assertFalse($this->replacer->canExtend($localFile, 'somethingelse'));
    }

    public function testReplaceTextReplacesASingleEntry()
    {
        $file = new LocalFile(static::$dir . 'simple_replace.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants');

        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testReplaceTextReplacesMultipleEntries()
    {
        $file = new LocalFile(static::$dir . 'multiple_replace.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, ['text','some'], ['pants','many']);

        static::assertNotNull($newFile);
        static::assertEquals(['many pants that pants should be replaced'], $newFile->getContents());
    }

    public function testReplaceTextReplacesMultipleEntriesWorksInCompound()
    {
        $file = new LocalFile(static::$dir . 'multiple_compound_replace.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, ['text','pants that'], ['pants','fish like']);

        static::assertNotNull($newFile);
        static::assertEquals(['some fish like pants should be replaced'], $newFile->getContents());
    }

    public function testCallingReplaceTextWithArraysThatHaveMismatchedCountsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'multiple_replace_failure.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        static::setExpectedException(
            'InvalidArgumentException',
            "Number of items in 'fromText' (2) is different to 'toText' (1)"
        );

        $this->replacer->replaceText($file, ['text','pants that'], ['pants']);
    }

    public function testCanCallReplaceTextAsAFlow()
    {
        $file = new LocalFile(static::$dir . 'flow_replace.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        $newFile = $file->replaceText('text', 'pants');

        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testAddingAPostfixToTheEndOfTheFile()
    {
        $file = new LocalFile(static::$dir . 'postfix_test.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['postfix' => 'pfixtest']);

        static::assertNotNull($newFile);
        static::assertEquals('postfix_test-pfixtest.test', $newFile->getFilename());
    }

    public function testCallingWithBlankPostfixWillReplaceInLine()
    {
        $file = new LocalFile(static::$dir . 'inline_replace.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['postfix' => '']);

        static::assertNotNull($newFile);
        static::assertEquals($file->getFilename(), $newFile->getFilename());
    }

    public function testSettingKeepOldFileToFalseWillDeleteTheOldFile()
    {
        $file = new LocalFile(static::$dir . 'inline_replace.test');
        file_put_contents($file->getFilePath(), 'some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['keepOldFile' => false]);

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
    }
}
