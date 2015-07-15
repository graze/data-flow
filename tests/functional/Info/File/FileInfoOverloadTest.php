<?php

namespace Graze\DataFlow\Test\Functional\Info\File;

use Graze\DataFlow\Flow\File\Compression\CompressionType;
use Graze\DataFlow\Info\File\FileInfo;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FileInfoOverloadTest extends FileTestCase
{
    /**
     * @var FileInfo
     */
    protected $fileInfo;

    public function setUp()
    {
        $this->fileInfo = new FileInfo();
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('isOutputDisabled')->andReturn(true);

        $file = new LocalFile(static::$dir . 'failed_find_encoding_process.test');
        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->fileInfo->findEncoding($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindCompression()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('isOutputDisabled')->andReturn(true);

        $file = new LocalFile(static::$dir . 'failed_find_encoding_process.test');
        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->fileInfo->findCompression($file);
    }

    public function testWhenTheProcessReturnsAnUnknownEncodingUnknownTypeIsReturned()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->andReturn('text/plain; charset=utf-8 compressed-encoding=application/lzop; charset=binary; charset=binary');

        $file = new LocalFile(static::$dir . 'unknown_compression.test');
        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::assertEquals(CompressionType::UNKNOWN, $this->fileInfo->findCompression($file));
    }

    public function testWhenTheProcessReturnsAnUnknownFileNullIsReturned()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->andReturn('some random stuff with no charset');

        $file = new LocalFile(static::$dir . 'unknown_compression.test');
        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::assertNull($this->fileInfo->findEncoding($file));
    }
}
