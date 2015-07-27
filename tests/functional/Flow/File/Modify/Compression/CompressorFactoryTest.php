<?php

namespace Graze\DataFlow\Test\Functional\Flow\File\Modify\Compression;

use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use Graze\DataFlow\Flow\File\Modify\Compression\CompressorFactory;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

class CompressorFactoryTest extends FileTestCase
{
    /**
     * @var CompressorFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new CompressorFactory();
    }

    public function testCanExtendOnlyAcceptsFileNodeInterfaceClass()
    {
        $node = m::mock('Graze\DataFlow\Node\File\FileNodeInterface','Graze\Extensible\ExtensibleInterface');
        $generic = m::mock('Graze\Extensible\ExtensibleInterface');

        static::assertTrue($this->factory->canExtend($node, 'compress'));
        static::assertFalse($this->factory->canExtend($generic, 'compress'));
    }

    public function testCanCompressGzipFile()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_gz.test');
        file_put_contents($file->getFilePath(), 'random stuff!');

        $compressedFile = $this->factory->compress($file, CompressionType::GZIP);

        static::assertNotNull($compressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $compressedFile);
        static::assertEquals(static::$dir . 'uncompressed_gz.gz', $compressedFile->getFilePath());
        static::assertTrue($compressedFile->exists());
        static::assertEquals(CompressionType::GZIP, $compressedFile->getCompression());

        $cmd = "file {$compressedFile->getFilePath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as gzip");
    }

    public function testCanDecompressGzipFile()
    {
        $file = new LocalFile(static::$dir . 'uncompressed2_gz.test');
        file_put_contents($file->getFilePath(), 'random stuff!');

        $compressedFile = $this->factory->compress($file, CompressionType::GZIP);
        $uncompressedFile = $this->factory->decompress($compressedFile);

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $uncompressedFile);
        static::assertEquals(static::$dir . 'uncompressed2_gz', $uncompressedFile->getFilePath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionType::NONE, $uncompressedFile->getCompression());
    }

    public function testCanCompressZipFile()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_zip.test');
        file_put_contents($file->getFilePath(), 'random stuff!');

        $compressedFile = $this->factory->compress($file, CompressionType::ZIP);

        static::assertNotNull($compressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $compressedFile);
        static::assertEquals(static::$dir . 'uncompressed_zip.zip', $compressedFile->getFilePath());
        static::assertTrue($compressedFile->exists());
        static::assertEquals(CompressionType::ZIP, $compressedFile->getCompression());

        $cmd = "file {$compressedFile->getFilePath()} | grep " . escapeshellarg('\bzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as gzip");
    }

    public function testCanDecompressZipFile()
    {
        $file = new LocalFile(static::$dir . 'uncompressed2_zip.test');
        file_put_contents($file->getFilePath(), 'random stuff!');

        $compressedFile = $this->factory->compress($file, CompressionType::ZIP);
        $uncompressedFile = $this->factory->decompress($compressedFile);

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $uncompressedFile);
        static::assertEquals(static::$dir . 'uncompressed2_zip', $uncompressedFile->getFilePath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionType::NONE, $uncompressedFile->getCompression());
    }

    public function testCanInvokeCompressUsingFlow()
    {
        $file = new LocalFile(static::$dir . 'invoked_gz.test');
        file_put_contents($file->getFilePath(), 'random stuff!');

        $compressedFile = $file->compress(CompressionType::GZIP);

        static::assertNotNull($compressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $compressedFile);
        static::assertEquals(static::$dir . 'invoked_gz.gz', $compressedFile->getFilePath());
        static::assertTrue($compressedFile->exists());
        static::assertEquals(CompressionType::GZIP, $compressedFile->getCompression());

        $cmd = "file {$compressedFile->getFilePath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as gzip");
    }

    public function testCanInvokeDecompressUsingFlow()
    {
        $file = new LocalFile(static::$dir . 'invoked_decompress_gz.test');
        file_put_contents($file->getFilePath(), 'random stuff!');

        $compressedFile = $this->factory->compress($file, CompressionType::GZIP);
        $uncompressedFile = $compressedFile->decompress();

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf('Graze\DataFlow\Node\File\FileNodeInterface', $uncompressedFile);
        static::assertEquals(static::$dir . 'invoked_decompress_gz', $uncompressedFile->getFilePath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionType::NONE, $uncompressedFile->getCompression());
    }

    public function testOptionsArePassedThroughToTheCompressor()
    {
        $file = new LocalFile(static::$dir . 'delete_old.test');
        file_put_contents($file->getFilePath(), 'random stuff!');

        $compressedFile = $this->factory->compress($file, CompressionType::GZIP, ['keepOldFile' => false]);

        static::assertFalse($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->factory->decompress($compressedFile, ['keepOldFile' => false]);

        static::assertFalse($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }

    public function testPassingNoneCompressionTypeThrowsException()
    {
        $node = m::mock('Graze\DataFlow\Node\File\FileNodeInterface','Graze\DataFlow\Flowable\FlowableInterface');

        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Modify\Compression\InvalidCompressionTypeException',
            'Unknown compression type: none'
        );

        $this->factory->compress($node, CompressionType::NONE);
    }

    public function testPassingInvalidCompressionTypeThrowsException()
    {
        $node = m::mock('Graze\DataFlow\Node\File\FileNodeInterface','Graze\DataFlow\Flowable\FlowableInterface');

        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Modify\Compression\InvalidCompressionTypeException',
            'Unknown compression type: random string'
        );

        $this->factory->compress($node, 'random string');
    }
}
