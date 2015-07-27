<?php

namespace Graze\DataFlow\Test\Functional\Info\File;

use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use Graze\DataFlow\Info\File\FileInfo;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Graze\DataFlow\Utility\ProcessFactory;
use Mockery as m;

class FileInfoTest extends FileTestCase
{
    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * @var ProcessFactory|m\MockInterface
     */
    protected $processFactory;

    public function setUp()
    {
        $this->processFactory = m::mock('Graze\DataFlow\Utility\ProcessFactory')->makePartial();
        $this->fileInfo = new FileInfo($this->processFactory);
    }

    public function testCanExtendAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->fileInfo->canExtend($localFile, 'findEncoding'));
        static::assertFalse(
            $this->fileInfo->canExtend($localFile, 'findEncoding'),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->fileInfo->canExtend($randomThing, 'toEncoding'));
    }

    public function testCanExtendOnlyAcceptsTheFindMethods()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true);

        static::assertTrue($this->fileInfo->canExtend($localFile, 'findEncoding'));
        static::assertTrue($this->fileInfo->canExtend($localFile, 'findCompression'));
        static::assertFalse($this->fileInfo->canExtend($localFile, 'somethingelse'));
    }

    public function testGetFileEncodingForASCIIFile()
    {
        $asciiFile = new LocalFile(static::$dir . 'ascii_file.test', ['encoding' => 'us-ascii']);
        file_put_contents($asciiFile->getFilePath(), mb_convert_encoding('Some random Text', 'ASCII'));

        static::assertEquals(
            strtolower($asciiFile->getEncoding()),
            strtolower($this->fileInfo->findEncoding($asciiFile))
        );
    }

    public function testGetFileEncodingForUtf8File()
    {
        $utf8file = new LocalFile(static::$dir . 'utf8_file.test', ['encoding' => 'UTF-8']);
        file_put_contents($utf8file->getFilePath(), mb_convert_encoding('Some random Text €±§', 'UTF-8'));

        static::assertEquals(
            strtolower($utf8file->getEncoding()),
            strtolower($this->fileInfo->findEncoding($utf8file))
        );
    }

    public function testGetFileCompressionForNonCompressedFile()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_file.test');
        file_put_contents($file->getFilePath(), 'some random text');

        static::assertEquals(
            $file->getCompression(),
            $this->fileInfo->findCompression($file)
        );
        static::assertEquals(CompressionType::NONE, $file->getCompression());
    }

    public function testGetFileCompressionForGzipFile()
    {
        $file = new LocalFile(static::$dir . 'tobegzipped_file.test');
        file_put_contents($file->getFilePath(), 'some random text');
        $gzipFile = $file->gzip();

        static::assertEquals(
            $gzipFile->getCompression(),
            $this->fileInfo->findCompression($gzipFile)
        );
        static::assertEquals(CompressionType::GZIP, $gzipFile->getCompression());
    }

    public function testGetFileCompressionForZipFile()
    {
        $file = new LocalFile(static::$dir . 'tobezipped.test');
        file_put_contents($file->getFilePath(), 'some random text');
        $zipFile = $file->zip();

        static::assertEquals(
            $zipFile->getCompression(),
            $this->fileInfo->findCompression($zipFile)
        );
        static::assertEquals(CompressionType::ZIP, $zipFile->getCompression());
    }

    public function testGetFileEncodingForCompressedFile()
    {
        $utf8file = new LocalFile(static::$dir . 'utf8_tobegzipped_file.test', ['encoding' => 'UTF-8']);
        file_put_contents($utf8file->getFilePath(), mb_convert_encoding('Some random Text €±§', 'UTF-8'));
        $gzipFile = $utf8file->gzip();

        static::assertEquals(
            strtolower($gzipFile->getEncoding()),
            strtolower($this->fileInfo->findEncoding($gzipFile))
        );
        static::assertEquals('utf-8', $gzipFile->findEncoding($gzipFile));
        static::assertEquals($utf8file->getEncoding(), $gzipFile->getEncoding());
    }

    public function testGetFileEncodingFlow()
    {
        $asciiFile = new LocalFile(static::$dir . 'ascii_file_flow.test', ['encoding' => 'us-ascii']);
        file_put_contents($asciiFile->getFilePath(), mb_convert_encoding('Some random Text', 'ASCII'));

        static::assertEquals(
            strtolower($asciiFile->getEncoding()),
            strtolower($asciiFile->findEncoding())
        );
    }

    public function testGetFileCompressionFlow()
    {
        $file = new LocalFile(static::$dir . 'tobegzipped_file_flow.test');
        file_put_contents($file->getFilePath(), 'some random text');
        $gzipFile = $file->gzip();

        static::assertEquals(
            $gzipFile->getCompression(),
            $gzipFile->findCompression()
        );
        static::assertEquals(CompressionType::GZIP, $gzipFile->getCompression());
    }


    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
    {
        $process = m::mock('Symfony\Component\Process\Process')->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);

        $this->processFactory->shouldReceive('createProcess')
            ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_find_encoding_process.test');
        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->fileInfo->findEncoding($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindCompression()
    {
        $process = m::mock('Symfony\Component\Process\Process')->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_find_encoding_process.test');
        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->fileInfo->findCompression($file);
    }

    public function testWhenTheProcessReturnsAnUnknownEncodingUnknownTypeIsReturned()
    {
        $process = m::mock('Symfony\Component\Process\Process')->makePartial();
        $process->shouldReceive('run');
        $process->shouldReceive('isSuccessful')->andReturn(true);
        $process->shouldReceive('getOutput')->andReturn('text/plain; charset=utf-8 compressed-encoding=application/lzop; charset=binary; charset=binary');

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'unknown_compression.test');
        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::assertEquals(CompressionType::UNKNOWN, $this->fileInfo->findCompression($file));
    }

    public function testWhenTheProcessReturnsAnUnknownFileNullIsReturned()
    {
        $process = m::mock('Symfony\Component\Process\Process')->makePartial();
        $process->shouldReceive('run');
        $process->shouldReceive('isSuccessful')->andReturn(true);
        $process->shouldReceive('getOutput')->andReturn('some random stuff with no charset');

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'unknown_compression.test');
        file_put_contents($file->getFilePath(), 'random stuff and things 2!');

        static::assertNull($this->fileInfo->findEncoding($file));
    }
}
