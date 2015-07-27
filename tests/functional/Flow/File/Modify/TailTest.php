<?php

namespace Graze\DataFlow\Test\Functional\Flow\File\Modify;

use Graze\DataFlow\Flow\File\Modify\Tail;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Graze\DataFlow\Utility\ProcessFactory;
use Mockery as m;
use Mockery\MockInterface;

class TailTest extends FileTestCase
{
    /**
     * @var Tail
     */
    protected $tail;

    /**
     * @var ProcessFactory|MockInterface
     */
    protected $processFactory;

    public function setUp()
    {
        $this->processFactory = m::mock('Graze\DataFlow\Utility\ProcessFactory')->makePartial();
        $this->tail = new Tail($this->processFactory);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\Extensible\ExtensionInterface', $this->tail);
        static::assertInstanceOf('Graze\DataFlow\Flow\File\Modify\FileModifierInterface', $this->tail);
    }

    public function testCanExtendAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->tail->canExtend($localFile, 'tail'));
        static::assertFalse(
            $this->tail->canExtend($localFile, 'tail'),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->tail->canExtend($randomThing, 'tail'));
    }

    public function testCanModifyAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->tail->canModify($localFile));
        static::assertFalse(
            $this->tail->canModify($localFile),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');

        static::assertFalse($this->tail->canModify($randomThing));
    }

    public function testCanExtendOnlyAcceptsTheCopyMethod()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true);

        static::assertTrue($this->tail->canExtend($localFile, 'tail'));
        static::assertFalse($this->tail->canExtend($localFile, 'somethingelse'));
    }

    public function testBasicReadingTheLastNLines()
    {
        $file = $this->createFile('last.five.lines');

        $newFile = $this->tail->tail($file, 5);

        static::assertEquals(
            [
                "Line 6\n",
                "Line 7\n",
                "Line 8\n",
                "Line 9\n",
                "Line 10\n"
            ],
            $newFile->getContents()
        );

        $newFile = $this->tail->tail($file, 2);

        static::assertEquals(
            [
                "Line 9\n",
                "Line 10\n"
            ],
            $newFile->getContents()
        );
    }

    /**
     * @param $path
     * @return LocalFile
     */
    private function createFile($path)
    {
        $file = new LocalFile(static::$dir . $path);
        file_put_contents(
            $file->getFilePath(),
            "Line 1
Line 2
Line 3
Line 4
Line 5
Line 6
Line 7
Line 8
Line 9
Line 10
"
        );

        return $file;
    }

    public function testOutputLinesStartingFromN()
    {
        $file = $this->createFile('from.second.line.onwards');

        $newFile = $this->tail->tail($file, '+2');

        static::assertEquals(
            [
                "Line 2\n",
                "Line 3\n",
                "Line 4\n",
                "Line 5\n",
                "Line 6\n",
                "Line 7\n",
                "Line 8\n",
                "Line 9\n",
                "Line 10\n"
            ],
            $newFile->getContents()
        );
    }

    public function testAddingAPostfixToTheEndOfTheFile()
    {
        $file = $this->createFile('postfix_test.test');

        $newFile = $this->tail->tail($file, 4, ['postfix' => 'pfixtest']);

        static::assertNotNull($newFile);
        static::assertEquals('postfix_test-pfixtest.test', $newFile->getFilename());
    }

    public function testCallingWithBlankPostfixWillReplaceInLine()
    {
        $file = $this->createFile('inline_tail.test');

        $newFile = $this->tail->tail($file, 2, ['postfix' => '']);

        static::assertNotNull($newFile);
        static::assertEquals($file->getFilename(), $newFile->getFilename());
    }

    public function testSettingKeepOldFileToFalseWillDeleteTheOldFile()
    {
        $file = $this->createFile('inline_replace.test');

        $newFile = $this->tail->tail($file, 5, ['keepOldFile' => false]);

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
    }

    public function testCallingModifyDoesTail()
    {
        $file = $this->createFile('simple_tail.test');

        $newFile = $this->tail->modify($file, ['lines' => 4]);

        static::assertEquals(
            [
                "Line 7\n",
                "Line 8\n",
                "Line 9\n",
                "Line 10\n"
            ],
            $newFile->getContents()
        );
    }

    public function testCallingModifyWillPassThroughOptions()
    {
        $file = $this->createFile('option_pass_through.test');

        $newFile = $this->tail->modify($file,
            [
                'lines'       => 2,
                'postfix'     => 'pass',
                'keepOldFile' => false
            ]
        );

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
        static::assertNotNull($newFile);
        static::assertEquals('option_pass_through-pass.test', $newFile->getFilename());
    }

    public function testCallingModifyWithoutLinesWillThrowAnException()
    {
        $file = $this->createFile('option_pass_through.test');

        static::setExpectedException(
            'InvalidArgumentException',
            "Missing option: 'lines'"
        );

        $this->tail->modify($file);
    }

    public function testCallingModifyWithANonLocalFileWillThrowAnException()
    {
        $file = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file->shouldReceive('__toString')
             ->andReturn('some/file/here');

        static::setExpectedException(
            'invalidArgumentException',
            "Supplied: some/file/here is not a LocalFile"
        );

        $this->tail->modify($file, ['lines' => 1]);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
    {
        $process = m::mock('Symfony\Component\Process\Process')->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $this->processFactory->shouldReceive('createProcess')
            ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_tail.test');
        file_put_contents($file->getFilePath(), 'nothing interesting here');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->tail->tail($file, 3);
    }
}
