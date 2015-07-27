<?php

namespace Graze\DataFlow\Test\Fuctional\Flow\File\Modify;

use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use Graze\DataFlow\Flow\File\Modify\Copy;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

class CopyTest extends FileTestCase
{
    /**
     * @var Copy
     */
    protected $copyer;

    public function setUp()
    {
        $this->copyer = new Copy();
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\Extensible\ExtensionInterface', $this->copyer);
        static::assertInstanceOf('Graze\DataFlow\Flow\File\Modify\FileModifierInterface', $this->copyer);
    }

    public function testCanExtendAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->copyer->canExtend($localFile, 'copy'));
        static::assertFalse(
            $this->copyer->canExtend($localFile, 'copy'),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->copyer->canExtend($randomThing, 'copy'));
    }

    public function testCanModifyAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->copyer->canModify($localFile));
        static::assertFalse(
            $this->copyer->canModify($localFile),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');

        static::assertFalse($this->copyer->canModify($randomThing));
    }

    public function testCanExtendOnlyAcceptsTheCopyMethod()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $localFile->shouldReceive('exists')->andReturn(true);

        static::assertTrue($this->copyer->canExtend($localFile, 'copy'));
        static::assertFalse($this->copyer->canExtend($localFile, 'somethingelse'));
    }

    public function testCopyCreatesADuplicateFile()
    {
        $localFile = new LocalFile(static::$dir . 'copy_orig.test');
        file_put_contents($localFile->getFilePath(), 'some random text');

        $newFile = $localFile->getClone()
                             ->setFilePath($localFile->getFilePath() . '.copy');

        $newFileReturn = $this->copyer->copy($localFile, $newFile);

        static::assertTrue($newFile->exists());
        static::assertSame($newFileReturn, $newFile);
        static::assertEquals($localFile->getFilePath() . '.copy', $newFile->getFilePath());
        static::assertEquals($localFile->getContents(), $newFile->getContents());
    }

    public function testCopyCopiesAttributes()
    {
        $localFile = new LocalFile(static::$dir . 'copy_attributes.text', ['encoding' => 'ascii']);
        file_put_contents($localFile, 'some ascii text');

        $newFile = $localFile->getClone()
                             ->setFilePath($localFile->getFilePath() . '.copy');
        $newFileReturn = $this->copyer->copy($localFile, $newFile);

        static::assertSame($newFileReturn, $newFile);
        static::assertEquals('ascii', $newFile->getEncoding());

        $gzipped = $newFile->gzip();

        static::assertEquals(CompressionType::GZIP, $gzipped->getCompression());

        $gzipCopy = $this->copyer->copy($gzipped, $gzipped->getClone()->setFilePath($gzipped->getFilePath() . '.copy'));

        static::assertEquals($gzipped->getCompression(), $gzipCopy->getCompression());
    }

    public function testWhenCopyFailsItRaisesAnException()
    {
        $localFile = new LocalFile(static::$dir . 'copy_failed.text', ['encoding' => 'ascii']);
        file_put_contents($localFile, 'some ascii text');

        $newFile = $localFile->getClone()
                             ->setFilePath('/not/a/real/path/' . $localFile->getFilename());

        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Modify\Exception\CopyFailedException',
            "Failed to copy file from: '$localFile' to '$newFile'. copy(/not/a/real/path/copy_failed.text): failed to open stream: No such file or directory"
        );

        $this->copyer->copy($localFile, $newFile);
    }

    public function testModifyWillCopyTheFile()
    {
        $localFile = new LocalFile(static::$dir . 'copy_modify.text');
        file_put_contents($localFile, 'some random things');

        $newFile = $this->copyer->modify($localFile, ['outputFilePath' => $localFile->getFilePath() . '.copy']);

        static::assertEquals($localFile->getFilePath() . '.copy', $newFile->getFilePath());
        static::assertTrue($newFile->exists());
    }

    public function testCallingModifyWithNoOptionsWillCreateAFileWithCopyPostfix()
    {
        $localFile = $localFile = new LocalFile(static::$dir . 'copy_modify.defauly');
        file_put_contents($localFile, 'some random things');

        $newFile = $this->copyer->modify($localFile);

        static::assertEquals($localFile->getFilePath() . '.copy', $newFile->getFilePath());
        static::assertTrue($newFile->exists());
    }

    public function testCallingModifyWithANonLocalFileWillThrowAnException()
    {
        $file = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file->shouldReceive('__toString')->andReturn('some/file/here');

        static::setExpectedException(
            'InvalidArgumentException',
            "Supplied some/file/here is not a LocalFile"
        );

        $this->copyer->modify($file, ['outputFilePath' => 'some/file/here/copy']);
    }

}
