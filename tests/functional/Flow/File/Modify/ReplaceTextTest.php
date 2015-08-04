<?php

namespace Graze\DataFlow\Test\Fuctional\Flow\File\Modify;

use Graze\DataFlow\Flow\File\Modify\ReplaceText;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Graze\DataFlow\Utility\Process\ProcessFactory;
use Mockery as m;
use Mockery\MockInterface;

class ReplaceTextTest extends FileTestCase
{
    /**
     * @var ReplaceText
     */
    protected $replacer;

    /**
     * @var ProcessFactory|MockInterface
     */
    protected $processFactory;

    public function setUp()
    {
        $this->processFactory = m::mock('Graze\DataFlow\Utility\Process\ProcessFactory')->makePartial();
        $this->replacer = new ReplaceText($this->processFactory);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\Extensible\ExtensionInterface', $this->replacer);
        static::assertInstanceOf('Graze\DataFlow\Flow\File\Modify\FileModifierInterface', $this->replacer);
    }

    public function testCanExtendAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->replacer->canExtend($localFile, 'replaceText'));
        static::assertFalse(
            $this->replacer->canExtend($localFile, 'replaceText'),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->replacer->canExtend($randomThing, 'replaceText'));
    }

    public function testCanModifyAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->replacer->canModify($localFile));
        static::assertFalse(
            $this->replacer->canModify($localFile),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');

        static::assertFalse($this->replacer->canModify($randomThing));
    }

    public function testCanExtendOnlyAcceptsTheReplaceTextMethod()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true);

        static::assertTrue($this->replacer->canExtend($localFile, 'replaceText'));
        static::assertFalse($this->replacer->canExtend($localFile, 'somethingelse'));
    }

    public function testReplaceTextReplacesASingleEntry()
    {
        $file = new LocalFile(static::$dir . 'simple_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants');

        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testReplaceTextReplacesMultipleEntries()
    {
        $file = new LocalFile(static::$dir . 'multiple_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, ['text', 'some'], ['pants', 'many']);

        static::assertNotNull($newFile);
        static::assertEquals(['many pants that pants should be replaced'], $newFile->getContents());
    }

    public function testReplaceTextReplacesMultipleEntriesWorksInCompound()
    {
        $file = new LocalFile(static::$dir . 'multiple_compound_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, ['text', 'pants that'], ['pants', 'fish like']);

        static::assertNotNull($newFile);
        static::assertEquals(['some fish like pants should be replaced'], $newFile->getContents());
    }

    public function testCallingReplaceTextWithArraysThatHaveMismatchedCountsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'multiple_replace_failure.test');
        $file->put('some text that text should be replaced');

        static::setExpectedException(
            'InvalidArgumentException',
            "Number of items in 'fromText' (2) is different to 'toText' (1)"
        );

        $this->replacer->replaceText($file, ['text', 'pants that'], ['pants']);
    }

    public function testCanCallReplaceTextAsAFlow()
    {
        $file = new LocalFile(static::$dir . 'flow_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $file->replaceText('text', 'pants');

        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testAddingAPostfixToTheEndOfTheFile()
    {
        $file = new LocalFile(static::$dir . 'postfix_test.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['postfix' => 'pfixtest']);

        static::assertNotNull($newFile);
        static::assertEquals('postfix_test-pfixtest.test', $newFile->getFilename());
    }

    public function testCallingWithBlankPostfixWillReplaceInLine()
    {
        $file = new LocalFile(static::$dir . 'inline_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['postfix' => '']);

        static::assertNotNull($newFile);
        static::assertEquals($file->getFilename(), $newFile->getFilename());
    }

    public function testSettingKeepOldFileToFalseWillDeleteTheOldFile()
    {
        $file = new LocalFile(static::$dir . 'inline_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['keepOldFile' => false]);

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
    }

    public function testCallingModifyReplacesText()
    {
        $file = new LocalFile(static::$dir . 'simple_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->modify($file, ['fromText' => 'text', 'toText' => 'pants']);

        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testCallingModifyWillPassThroughOptions()
    {
        $file = new LocalFile(static::$dir . 'option_pass_through.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->modify($file,
            [
                'fromText'    => 'text',
                'toText'      => 'pants',
                'postfix'     => 'pass',
                'keepOldFile' => false
            ]
        );

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
        static::assertNotNull($newFile);
        static::assertEquals('option_pass_through-pass.test', $newFile->getFilename());
    }

    public function testCallingModifyWithNoFromTextThrowsInvalidArgumentsException()
    {
        static::setExpectedException(
            "InvalidArgumentException",
            "Missing option: 'fromText'"
        );

        $file = new LocalFile(static::$dir . 'simple_replace.test');
        $file->put('some text that text should be replaced');

        $this->replacer->modify($file, ['toText' => 'pants']);
    }

    public function testCallingModifyWithNoToTextThrowsInvalidArgumentsException()
    {
        static::setExpectedException(
            "InvalidArgumentException",
            "Missing option: 'toText'"
        );

        $file = new LocalFile(static::$dir . 'simple_replace.test');
        $file->put('some text that text should be replaced');

        $this->replacer->modify($file, ['fromText' => 'pants']);
    }

    public function testCallingModifyWithANonLocalFileWillThrowAnException()
    {
        $file = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file->shouldReceive('__toString')
             ->andReturn('some/file/here');

        static::setExpectedException(
            "InvalidArgumentException",
            "Supplied: some/file/here is not a LocalFile"
        );

        $this->replacer->modify($file, ['fromText' => 'pants', 'toText' => 'more pants']);
    }

    public function testCallingReplaceTextOnAFileWithoutAnExtensionWorks()
    {
        $file = new LocalFile(static::$dir . 'file_no_ext');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants');

        static::assertTrue($newFile->exists());
        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
    {
        $process = m::mock('Symfony\Component\Process\Process')->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_replace_text.test');
        $file->put('some text that text should be replaced');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->replacer->replaceText($file, 'text', 'pants');
    }
}
