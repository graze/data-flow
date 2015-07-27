<?php

namespace Graze\DataFlow\Test\Functional\Flow\File\Expand;

use Graze\DataFlow\Flow\File\Expand\SplitFile;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Graze\DataFlow\Utility\ProcessFactory;
use Mockery as m;
use Mockery\MockInterface;

class SplitFileTest extends FileTestCase
{
    /**
     * @var SplitFile
     */
    protected $split;

    /**
     * @var ProcessFactory|MockInterface
     */
    protected $processFactory;

    public function setUp()
    {
        $this->processFactory = m::mock('Graze\DataFlow\Utility\ProcessFactory')->makePartial();
        $this->split = new SplitFile($this->processFactory);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\Extensible\ExtensionInterface', $this->split);
        static::assertInstanceOf('Graze\DataFlow\Flow\File\Expand\FileExpanderInterface', $this->split);
    }

    public function testCanExtendAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->split->canExtend($localFile, 'splitIntoParts'));
        static::assertFalse(
            $this->split->canExtend($localFile, 'splitIntoParts'),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->split->canExtend($randomThing, 'splitIntoParts'));
    }

    public function testCanModifyAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->split->canExpand($localFile));
        static::assertFalse(
            $this->split->canExpand($localFile),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');

        static::assertFalse($this->split->canExpand($randomThing));
    }

    public function testCanExtendOnlyAcceptsTheCopyMethod()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true);

        static::assertTrue($this->split->canExtend($localFile, 'splitIntoParts'));
        static::assertTrue($this->split->canExtend($localFile, 'splitByLines'));
        static::assertFalse($this->split->canExtend($localFile, 'somethingelse'));
    }

    public function testCanSplitAFileInto2Parts()
    {
        $file = $this->createFile('simple.4.line.txt', 4);

        $splitFiles = $this->split->splitIntoParts($file, 2);

        static::assertEquals(2, $splitFiles->count());
        $first = $splitFiles->getAll()[0];
        $second = $splitFiles->getAll()[1];

        static::assertEquals(["Line 1\n", "Line 2\n"], $first->getContents());
        static::assertEquals(["Line 3\n", "Line 4\n"], $second->getContents());
    }

    /**
     * @param string $filename
     * @param int    $lines
     * @return LocalFile
     */
    private function createFile($filename, $lines)
    {
        $file = new LocalFile(static::$dir . $filename);
        $output = '';
        for ($i = 1; $i <= $lines; $i++) {
            $output .= "Line $i\n";
        }
        file_put_contents($file, $output);
        return $file;
    }

    public function testCanSplitAFileByLines()
    {
        $file = $this->createFile('simple.5.line.txt', 5);

        $splitFiles = $this->split->splitByLines($file, 3);

        static::assertEquals(2, $splitFiles->count());
        $first = $splitFiles->getAll()[0];
        $second = $splitFiles->getAll()[1];

        static::assertEquals(["Line 1\n", "Line 2\n", "Line 3\n"], $first->getContents());
        static::assertEquals(["Line 4\n", "Line 5\n"], $second->getContents());
    }

    public function testSplitIntoPartsUsesGNUCommandForGNUSystem()
    {
        $process = m::mock('Symfony\Process\Process');
        $this->processFactory->shouldReceive('createProcess')
                             ->with('split --version')
                             ->andReturn($process);

        $process->shouldReceive('run')->once();
        $process->shouldReceive('getOutput')
                ->andReturn("split (GNU coreutils) 8.21
Copyright (C) 2013 Free Software Foundation, Inc.
License GPLv3+: GNU GPL version 3 or later <http://gnu.org/licenses/gpl.html>.
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.

Written by Torbjörn Granlund and Richard M. Stallman.");

        $file = $this->createFile('gnu.split.4.line.txt', 4);

        $splitProcess = m::mock('Symfony\Component\Process\Process')->makePartial();
        $this->processFactory->shouldReceive('createProcess')
                             ->with('/^split \-n l\/2 \-d \-e \-\-additional\-suffix=\.txt .+ .+/')
                             ->andReturn($splitProcess);

        $splitProcess->shouldReceive('isSuccessful')->atLeast()->once()->andReturn(false);

        // set exception as no guarantee process will run on local system
        static::setExpectedException('Symfony\Component\Process\Exception\ProcessFailedException');

        $this->split->splitIntoParts($file, 2);
    }

    public function testSplitByLinesUsesGNUCommandForGNUSystem()
    {
        $process = m::mock('Symfony\Process\Process');
        $this->processFactory->shouldReceive('createProcess')
                             ->with('split --version')
                             ->andReturn($process);

        $process->shouldReceive('run')->once();
        $process->shouldReceive('getOutput')
                ->andReturn("split (GNU coreutils) 8.21
Copyright (C) 2013 Free Software Foundation, Inc.
License GPLv3+: GNU GPL version 3 or later <http://gnu.org/licenses/gpl.html>.
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.

Written by Torbjörn Granlund and Richard M. Stallman.");

        $file = $this->createFile('gnu.split.by.line.4.line.txt', 4);

        $splitProcess = m::mock('Symfony\Component\Process\Process')->makePartial();
        $this->processFactory->shouldReceive('createProcess')
                             ->with('/^split \-l 2 \-d \-e \-\-additional\-suffix=\.txt .+ .+/')
                             ->andReturn($splitProcess);

        $splitProcess->shouldReceive('isSuccessful')->atLeast()->once()->andReturn(false);

        // set exception as no guarantee process will run on local system
        static::setExpectedException('Symfony\Component\Process\Exception\ProcessFailedException');

        $this->split->splitByLines($file, 2);
    }

    public function testSplitIntoPartsUsesUnixCommandForNonGNUSystem()
    {
        $process = m::mock('Symfony\Process\Process');
        $this->processFactory->shouldReceive('createProcess')
                             ->with('split --version')
                             ->andReturn($process);

        $process->shouldReceive('run')->once();
        $process->shouldReceive('getOutput')
                ->andReturn("split: illegal option -- -
usage: split [-a sufflen] [-b byte_count] [-l line_count] [-p pattern]
             [file [prefix]]");

        $file = $this->createFile('unix.split.4.line.txt', 4);

        $splitProcess = m::mock('Symfony\Component\Process\Process')->makePartial();
        $this->processFactory->shouldReceive('createProcess')
                             ->with('/^split \-l 2 .+ .+/')
                             ->andReturn($splitProcess);

        $splitProcess->shouldReceive('isSuccessful')->atLeast()->once()->andReturn(false);

        // set exception as no guarantee process will run on local system
        static::setExpectedException('Symfony\Component\Process\Exception\ProcessFailedException');

        $this->split->splitIntoParts($file, 2);
    }

    public function testSplitByLinesUsesUnixCommandForNonGNUSystem()
    {
        $process = m::mock('Symfony\Process\Process');
        $this->processFactory->shouldReceive('createProcess')
                             ->with('split --version')
                             ->andReturn($process);

        $process->shouldReceive('run')->once();
        $process->shouldReceive('getOutput')
                ->andReturn("split: illegal option -- -
usage: split [-a sufflen] [-b byte_count] [-l line_count] [-p pattern]
             [file [prefix]]");

        $file = $this->createFile('unix.split.by.line.4.line.txt', 4);

        $splitProcess = m::mock('Symfony\Component\Process\Process')->makePartial();
        $this->processFactory->shouldReceive('createProcess')
                             ->with('/^split \-l 2 .+ .+/')
                             ->andReturn($splitProcess);

        $splitProcess->shouldReceive('isSuccessful')->atLeast()->once()->andReturn(false);

        // set exception as no guarantee process will run on local system
        static::setExpectedException('Symfony\Component\Process\Exception\ProcessFailedException');

        $this->split->splitByLines($file, 2);
    }

    public function testSettingKeepOldFileToFalseWillDeleteTheOldFile()
    {
        $file = $this->createFile('simple.to.delete.5.line.txt', 5);

        $this->split->splitByLines($file, 3, ['keepOldFile' => false]);

        static::assertFalse($file->exists());
    }

    public function testCallingExpandWithSplitIntoPartsWillSplitIntoParts()
    {
        $file = $this->createFile('simple.expand.5.line.txt', 5);

        $splitFiles = $this->split->expand($file, ['numParts' => 2]);

        static::assertEquals(2, $splitFiles->count());
        $first = $splitFiles->getAll()[0];
        $second = $splitFiles->getAll()[1];

        static::assertEquals(["Line 1\n", "Line 2\n", "Line 3\n"], $first->getContents());
        static::assertEquals(["Line 4\n", "Line 5\n"], $second->getContents());
    }

    public function testCallingExpandWithSplitByLinesWillSplitByLines()
    {
        $file = $this->createFile('simple.split.by.lines.5.line.txt', 5);

        $splitFiles = $this->split->expand($file, ['byLines' => 3]);

        static::assertEquals(2, $splitFiles->count());
        $first = $splitFiles->getAll()[0];
        $second = $splitFiles->getAll()[1];

        static::assertEquals(["Line 1\n", "Line 2\n", "Line 3\n"], $first->getContents());
        static::assertEquals(["Line 4\n", "Line 5\n"], $second->getContents());
    }

    public function testCallingExpandWillPassThroughOptions()
    {
        $file = $this->createFile('passthrough.4.line.txt', 4);

        $splitFiles = $this->split->expand(
            $file,
            [
                'numParts'    => 2,
                'keepOldFile' => false,
                'postfix'     => 'customSplit'
            ]
        );

        static::assertEquals(2, $splitFiles->count());
        $first = $splitFiles->getAll()[0];
        $second = $splitFiles->getAll()[1];

        static::assertEquals(["Line 1\n", "Line 2\n"], $first->getContents());
        static::assertEquals(["Line 3\n", "Line 4\n"], $second->getContents());

        static::assertFalse($file->exists());
        static::assertEquals(realpath($file->getDirectory() . 'passthrough.4.line-customSplit/') . '/',
            $first->getDirectory());
    }

    public function testCallingExpandWithNoOptionsWillThrowAnException()
    {
        $file = $this->createFile('no.options.failure.4.line.txt', 4);

        static::setExpectedException(
            'InvalidArgumentException',
            "Either 'numParts' or 'byLines' should be specified in the options"
        );

        $this->split->expand($file);
    }

    public function testCallingExpandWithAnInvalidFileWillThrowAnException()
    {
        $file = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file->shouldReceive('__toString')
             ->andReturn('some/path/file');

        static::setExpectedException(
            'invalidArgumentException',
            "The specified some/path/file is not a LocalFile"
        );

        $this->split->expand($file, ['numParts' => 2]);
    }

    public function testCallingGetCommandForGNUWithInvalidTypeWillThrowException()
    {
        static::setExpectedException(
            'InvalidArgumentException',
            'Unknown Type: wibble'
        );

        static::invokeMethod($this->split, 'getCommand', [
            SplitFile::VERSION_GNU,
            'wibble',
            2,
            'txt',
            'some/path',
            'some/path/prefix'
        ]);
    }

    public function testCallingGetCommandForUnixWithInvalidTypeWillThrowException()
    {
        static::setExpectedException(
            'InvalidArgumentException',
            'Unknown Type: wibble'
        );

        static::invokeMethod($this->split, 'getCommand', [
            SplitFile::VERSION_UNIX,
            'wibble',
            2,
            'txt',
            'some/path',
            'some/path/prefix'
        ]);
    }

    public function testCallingGetCommandWithInvalidVersionWillThrowException()
    {
        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Exception\UnknownVersionException',
            'The version of split could not be determined from: complete_rubbish'
        );

        static::invokeMethod($this->split, 'getCommand', [
            'complete_rubbish',
            'wibble',
            2,
            'txt',
            'some/path',
            'some/path/prefix'
        ]);
    }
}
