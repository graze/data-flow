<?php

namespace Graze\DataFlow\Test\Functional\Flow\File\Transfer;

use Graze\DataFlow\Flow\File\Transfer\FileTransferInterface;
use Graze\DataFlow\Flow\File\Transfer\Transfer;
use Graze\DataFlow\Node\File\FileNode;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Mockery as m;

class TransferTest extends FileTestCase
{
    /**
     * @var FileTransferInterface
     */
    protected $transfer;

    public function setUp()
    {
        $this->transfer = new Transfer();
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\DataFlow\Flow\File\Transfer\FileTransferInterface', $this->transfer);
        static::assertInstanceOf('Graze\Extensible\ExtensionInterface', $this->transfer);
    }

    public function testCanExtendAcceptsLocalFile()
    {
        $file = m::mock('Graze\DataFlow\Node\File\FileNode');

        static::assertTrue($this->transfer->canExtend($file, 'copyTo'));

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->transfer->canExtend($randomThing, 'makeDirectory'));
    }

    public function testCanExtendOnlyAcceptsTheCopyMethod()
    {
        $file = m::mock('Graze\DataFlow\Node\File\FileNode');

        static::assertTrue($this->transfer->canExtend($file, 'copyTo'));
        static::assertTrue($this->transfer->canExtend($file, 'moveTo'));
        static::assertFalse($this->transfer->canExtend($file, 'somethingElse'));
    }

    public function testCopyBetweenFileSystems()
    {
        $fromFile = new LocalFile(static::$dir . 'from_between.text');
        $fromFile->put('Some Text In Here');

        $toFile = new FileNode(new Filesystem(new MemoryAdapter()), 'some_file');

        $this->transfer->copyTo($fromFile, $toFile);

        static::assertEquals('Some Text In Here', $toFile->read());
        static::assertEquals($fromFile->read(), $toFile->read());
    }

    public function testCopyBetweenSameFileSystem()
    {
        $fromFile = new LocalFile(static::$dir . 'from_same.text');
        $fromFile->put('Some Text In Here');

        $toFile = new LocalFile(static::$dir . 'to_same.text');

        $this->transfer->copyTo($fromFile, $toFile);

        static::assertEquals('Some Text In Here', $toFile->read());
        static::assertEquals($fromFile->read(), $toFile->read());
    }

    public function testMoveDeletesTheOldFile()
    {
        $fromFile = new LocalFile(static::$dir . 'delete_from.text');
        $fromFile->put('Some Text In Here');

        $toFile = new LocalFile(static::$dir . 'delete_to.text');

        $this->transfer->moveTo($fromFile, $toFile);

        static::assertEquals('Some Text In Here', $toFile->read());
        static::assertFalse($fromFile->exists());
    }

    public function testCopyWhenOriginalFileDoesNotExistThrowsAnException()
    {
        $fromFile = new LocalFile(static::$dir . 'fail_from.text');

        $toFile = new LocalFile(static::$dir . 'fail_to.text');

        static::setExpectedException(
            'League\FlySystem\FileNotFoundException',
            "File not found at path: " . realpath($fromFile)
        );

        $this->transfer->copyTo($fromFile, $toFile);
    }

    public function testMoveWhenOriginalFileDoesNotExistThrowsAnException()
    {
        $fromFile = new LocalFile(static::$dir . 'fail_move_from.text');

        $toFile = new LocalFile(static::$dir . 'fail_move_to.text');

        static::setExpectedException(
            'League\FlySystem\FileNotFoundException',
            "File not found at path: " . realpath($fromFile)
        );

        $this->transfer->moveTo($fromFile, $toFile);
    }

    public function testCopyWhenFilesystemDoesNotReadStreamThrowsAnException()
    {
        $filesystem = m::mock('League\Flysystem\FileSystemInterface')->makePartial();

        $fromFile = new FileNode($filesystem, 'some/file');

        $toFile = new LocalFile(static::$dir . 'fail_copy_file.text');

        $filesystem->shouldReceive('readStream')->with($fromFile->getPath())->andReturn(false);

        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Exception\TransferFailedException',
            "Failed to transfer file: $fromFile to $toFile. "
        );

        $this->transfer->copyTo($fromFile, $toFile);
    }

    public function testMoveWhenFilesystemDoesNotReadStreamThrowsAnException()
    {
        $filesystem = m::mock('League\Flysystem\FileSystemInterface')->makePartial();

        $fromFile = new FileNode($filesystem, 'some/file');

        $toFile = new LocalFile(static::$dir . 'fail_move_file.text');

        $filesystem->shouldReceive('readStream')->with($fromFile->getPath())->andReturn(false);

        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Exception\TransferFailedException',
            "Failed to transfer file: $fromFile to $toFile. "
        );

        $this->transfer->moveTo($fromFile, $toFile);
    }
}
