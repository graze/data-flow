<?php

namespace Graze\DataFlow\Test\Functional\Flow\File;

use Graze\DataFlow\Flow\File\Compression\CompressionType;
use Graze\DataFlow\Flow\File\Compression\Zip;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ZipTest extends FileTestCase
{
    /**
     * @var Zip
     */
    protected $zip;

    public function setUp()
    {
        $this->zip = new Zip();
    }

    public function testInstanceOfCompressorInterface()
    {
        static::assertInstanceOf('Graze\DataFlow\Flow\File\Compression\CompressorInterface', $this->zip);
    }

    public function testCanFlowOnlyAcceptsFilesThatAreUnCompressedForZip()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_file_zip.test');
        file_put_contents($file->getFilePath(), 'random stuff and things!');

        static::assertTrue($this->zip->canExtend($file, 'zip'));
        static::assertFalse($this->zip->canExtend($file, 'unzip'));
    }

    public function testCanFlowOnlyAcceptsFilesThatAreCompressedForUnzip()
    {
        $file = new LocalFile(static::$dir . 'compressed_file_zip.zip', ['compression' => CompressionType::ZIP]);
        file_put_contents($file->getFilePath(), 'random stuff and things!');

        static::assertTrue($this->zip->canExtend($file, 'unzip'));
        static::assertFalse($this->zip->canExtend($file, 'zip'));
    }

    public function testCanFlowOnlyAcceptsLocalFiles()
    {
        $file = m::mock('Graze\DataFlow\Node\DataNode');
        static::assertFalse($this->zip->canExtend($file, 'zip'));
        static::assertFalse($this->zip->canExtend($file, 'unzip'));
    }

    public function testFileGetsCompressedAsZip()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_zip.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->zip->zip($file);

        static::assertNotNull($compressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $compressedFile);
        static::assertEquals(static::$dir . 'uncompressed_zip.zip', $compressedFile->getFilePath());
        static::assertTrue($compressedFile->exists());
        static::assertEquals(CompressionType::ZIP, $compressedFile->getCompression());

        $cmd = "file {$compressedFile->getFilePath()} | grep " . escapeshellarg('\bzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as zip");
    }

    public function testFileGetsDecompressedFromZip()
    {
        $file = new LocalFile(static::$dir . 'get_decompressed_uncompressed_zip.test');
        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->zip->zip($file);

        static::assertTrue($compressedFile->exists());
        $uncompressedFile = $this->zip->unzip($compressedFile);

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $uncompressedFile);
        static::assertEquals(static::$dir . 'get_decompressed_uncompressed_zip', $uncompressedFile->getFilePath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionType::NONE, $uncompressedFile->getCompression());

        $cmd = "file {$uncompressedFile->getFilePath()} | grep " . escapeshellarg('\bzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(0, $result, "File should not be compressed");
    }

    public function testFlowZipInvokation()
    {
        $file = new LocalFile(static::$dir . 'invokation_zip.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $file->zip();

        static::assertNotNull($compressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $compressedFile);
        static::assertEquals(static::$dir . 'invokation_zip.zip', $compressedFile->getFilePath());
        static::assertTrue($compressedFile->exists());

        $cmd = "file {$compressedFile->getFilePath()} | grep " . escapeshellarg('\bzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as zip");
    }

    public function testFlowUnzipInvokation()
    {
        $file = new LocalFile(static::$dir . 'invokation_unzip.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->zip->zip($file);
        $uncompressedFile = $compressedFile->unzip();

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $uncompressedFile);
        static::assertEquals(static::$dir . 'invokation_unzip', $uncompressedFile->getFilePath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionType::NONE, $uncompressedFile->getCompression());
    }

    public function testCallingZipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_zip.test');

        static::setExpectedException(
            'InvalidArgumentException',
            'The file: ' . $file->getFilePath() . ' does not exist'
        );

        $this->zip->zip($file);
    }

    public function testCallingUnzipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_zip.zip');

        static::setExpectedException(
            'InvalidArgumentException',
            'The file: ' . $file->getFilePath() . ' does not exist'
        );

        $this->zip->unzip($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsthrownOnZip()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('isOutputDisabled')->andReturn(true);

        $file = new LocalFile(static::$dir . 'failed_zip_process.test');

        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->zip->zip($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsthrownOnUnzip()
    {
        $process = m::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('isOutputDisabled')->andReturn(true);

        $file = new LocalFile(static::$dir . 'failed_unzip_process.test');

        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->zip->unzip($file);
    }


    public function testPassingTheKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'keep_file_zip.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->zip->zip($file, ['keepOldFile' => true]);

        static::assertTrue($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->zip->unzip($compressedFile, ['keepOldFile' => true]);

        static::assertTrue($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }

    public function testPassingFalseToKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'delete_file_zip.test');

        file_put_contents($file->getFilePath(), 'random stuff and things!');

        $compressedFile = $this->zip->zip($file, ['keepOldFile' => false]);

        static::assertFalse($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->zip->unzip($compressedFile, ['keepOldFile' => false]);

        static::assertFalse($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }
}
