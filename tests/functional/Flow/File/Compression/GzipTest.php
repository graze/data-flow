<?php

namespace Graze\DataFlow\Test\Functional\Flow\File;

use Graze\DataFlow\Flow\File\Compression\CompressionType;
use Graze\DataFlow\Flow\File\Compression\Gzip;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class GzipTest extends FileTestCase
{
    /**
     * @var Gzip
     */
    protected $gzip;

    public function setUp()
    {
        $this->gzip = new Gzip();
    }

    public function testInstanceOfCompressorInterface()
    {
        static::assertInstanceOf('Graze\DataFlow\Flow\File\Compression\CompressorInterface', $this->gzip);
    }

    public function testCanFlowOnlyAcceptsFilesThatAreUnCompressedForGzip()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_file_gz.test');
        file_put_contents($file->getFilePath(), 'random stuff and things!');

        static::assertTrue($this->gzip->canExtend($file, 'gzip'));
        static::assertFalse($this->gzip->canExtend($file, 'gunzip'));
    }

    public function testCanFlowOnlyAcceptsFilesThatAreCompressedForGunzip()
    {
        $file = new LocalFile(static::$dir . 'compressed_file_gz.gz', ['compression' => CompressionType::GZIP]);
        file_put_contents($file->getFilePath(), 'random stuff and things!');

        static::assertTrue($this->gzip->canExtend($file, 'gunzip'));
        static::assertFalse($this->gzip->canExtend($file, 'gzip'));
    }

    public function testCanFlowOnlyAcceptsLocalFiles()
    {
        $file = m::mock('Graze\DataFlow\Node\DataNode');
        static::assertFalse($this->gzip->canExtend($file, 'gzip'));
        static::assertFalse($this->gzip->canExtend($file, 'gunzip'));
    }

    public function testFileGetsCompressedAsGzip()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_gz.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->gzip->gzip($file);

        static::assertNotNull($compressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $compressedFile);
        static::assertEquals(static::$dir . 'uncompressed_gz.gz', $compressedFile->getFilePath());
        static::assertTrue($compressedFile->exists());
        static::assertEquals(CompressionType::GZIP, $compressedFile->getCompression());

        $cmd = "file {$compressedFile->getFilePath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as gzip");
    }

    public function testFileGetsDecompressedFromGzip()
    {
        $file = new LocalFile(static::$dir . 'get_decompressed_uncompressed_gz.test');
        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->gzip->gzip($file);
        $uncompressedFile = $this->gzip->gunzip($compressedFile);

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $uncompressedFile);
        static::assertEquals(static::$dir . 'get_decompressed_uncompressed_gz', $uncompressedFile->getFilePath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionType::NONE, $uncompressedFile->getCompression());

        $cmd = "file {$uncompressedFile->getFilePath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(0, $result, "File should not be compressed");
    }

    public function testFlowGzipInvokation()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_gz.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $file->gzip();

        static::assertNotNull($compressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $compressedFile);
        static::assertEquals(static::$dir . 'uncompressed_gz.gz', $compressedFile->getFilePath());
        static::assertTrue($compressedFile->exists());

        $cmd = "file {$compressedFile->getFilePath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as gzip");
    }

    public function testFlowGunzipInvokation()
    {
        $file = new LocalFile(static::$dir . 'get_decompressed_uncompressed_gz_invoke.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->gzip->gzip($file);
        $uncompressedFile = $compressedFile->gunzip();

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $uncompressedFile);
        static::assertEquals(static::$dir . 'get_decompressed_uncompressed_gz_invoke', $uncompressedFile->getFilePath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionType::NONE, $uncompressedFile->getCompression());
    }

    public function testCallingGzipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_gzip.test');

        static::setExpectedException(
            'InvalidArgumentException',
            'The file: '. $file->getFilePath() . ' does not exist'
        );

        $this->gzip->gzip($file);
    }

    public function testCallingGunzipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_gunzip.test');

        static::setExpectedException(
            'InvalidArgumentException',
            'The file: '. $file->getFilePath() . ' does not exist'
        );

        $this->gzip->gunzip($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnGzip()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('isOutputDisabled')->andReturn(true);

        $file = new LocalFile(static::$dir . 'failed_gzip_process.test');

        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->gzip->gzip($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnGunzip()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('isOutputDisabled')->andReturn(true);

        $file = new LocalFile(static::$dir . 'failed_gunzip_process.test');

        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->gzip->gunzip($file);
    }

    public function testPassingTheKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'keep_file_gz.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->gzip->gzip($file, ['keepOldFile' => true]);

        static::assertTrue($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->gzip->gunzip($compressedFile, ['keepOldFile' => true]);

        static::assertTrue($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }

    public function testPassingFalseToKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'delete_file_gz.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->gzip->gzip($file, ['keepOldFile' => false]);

        static::assertFalse($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->gzip->gunzip($compressedFile, ['keepOldFile' => false]);

        static::assertFalse($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }
}
