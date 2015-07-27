<?php

namespace Graze\DataFlow\Test\Functional\Flow\File\Collection;

use Graze\DataFlow\Flow\File\Collection\AddFilesFromDirectory;
use Graze\DataFlow\Flow\File\MakeDirectory;
use Graze\DataFlow\Node\File\FileNodeCollection;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

class AddFilesFromDirectoryTest extends FileTestCase
{
    /**
     * @var AddFilesFromDirectory
     */
    protected $populator;

    public function setUp()
    {
        MakeDirectory::aware();
        $this->populator = new AddFilesFromDirectory();
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\Extensible\ExtensionInterface', $this->populator);
    }

    public function testCanExtendAcceptsFileNodeCollection()
    {
        $collection = m::mock('Graze\DataFlow\Node\File\FileNodeCollectionInterface');

        static::assertTrue($this->populator->canExtend($collection, 'addFilesFromDirectory'));

        $randomThing = m::mock('Graze\DataFlow\Node\DataNodeCollection',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->populator->canExtend($randomThing, 'addFilesFromDirectory'));
    }

    public function testCanExtendOnlyAcceptsTheCopyMethod()
    {
        $collection = m::mock('Graze\DataFlow\Node\File\FileNodeCollectionInterface');

        static::assertTrue($this->populator->canExtend($collection, 'addFilesFromDirectory'));
        static::assertFalse($this->populator->canExtend($collection, 'somethingelse'));
    }

    public function testAddingFilesInAnEmptyDirectoryWillReturnAnEmptyCollection()
    {
        $directory = new LocalFile(static::$dir . 'empty.dir/');
        $directory->makeDirectory();

        $collection = new FileNodeCollection();
        $collection = $this->populator->addFilesFromDirectory(
            $collection,
            $directory->getDirectory(),
            function ($path) {
                return new LocalFile($path);
            }
        );

        static::assertEquals(0, $collection->count(), "The collection should be empty for a non exists directory");
    }

    public function testAddingFilesInANonExistentDirectoryWillThrowAnException()
    {
        $directory = static::$dir . 'non.existant.dir/';

        $collection = new FileNodeCollection();

        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Exception\DirectoryDoesNotExistException',
            "The directory: '$directory' does not exist. "
        );

        $this->populator->addFilesFromDirectory(
            $collection,
            $directory,
            function ($path) {
                return new LocalFile($path);
            }
        );
    }

    public function testCanAddASingleFileInADirectory()
    {
        $file = new LocalFile(static::$dir . 'single/file.here');
        $file->makeDirectory();
        file_put_contents($file->getFilePath(), 'some stuff');

        $collection = new FileNodeCollection();

        $this->populator->addFilesFromDirectory(
            $collection,
            $file->getDirectory(),
            function ($path) {
                return new LocalFile($path);
            }
        );

        static::assertEquals(1, $collection->count());

        $first = $collection->getAll()[0];

        static::assertEquals(realpath($file->getFilePath()), $first->getFilePath());
    }

    public function testCanAddMultipleFilesInADirectory()
    {
        $getFile = function ($num) {
            $file = new LocalFile(static::$dir . 'multiple/file.here' . $num);
            $file->makeDirectory();
            file_put_contents($file->getFilePath(), 'some stuff');
            return $file;
        };
        $file0 = $getFile(0);
        $file1 = $getFile(1);
        $file2 = $getFile(2);

        $collection = new FileNodeCollection();

        $this->populator->addFilesFromDirectory(
            $collection,
            $file0->getDirectory(),
            function ($path) {
                return new LocalFile($path);
            }
        );

        static::assertEquals(3, $collection->count());

        $first = $collection->getAll()[0];

        static::assertEquals(realpath($file0->getFilePath()), $first->getFilePath());
        static::assertEquals(
            array_map(function ($file) {
                return $file->getFilename();
            }, [$file0, $file1, $file2]),
            $collection->map(function ($file) {
                return $file->getFilename();
            })
        );
    }

    public function testOnlyAddsSingleLevelWhenRecursiveIsOff()
    {
        $validFile = new LocalFile(static::$dir . 'recursive/single.level.works');
        $validFile->makeDirectory();
        file_put_contents($validFile, 'some things');

        $subFolderFile = new LocalFile(static::$dir . 'recursive/multiple/level.doesnt.work');
        $subFolderFile->makeDirectory();
        file_put_contents($subFolderFile, 'other things');

        $collection = new FileNodeCollection();
        $this->populator->addFilesFromDirectory(
            $collection,
            $validFile->getDirectory(),
            function ($path) {
                return new LocalFile($path);
            },
            false
        );

        static::assertEquals(1, $collection->count());

        $first = $collection->getAll()[0];

        static::assertEquals(realpath($validFile->getFilePath()), $first->getFilePath());
    }

    public function testAddsAllChildFilesToTheSameCollectionWhenRecursiveIsOn()
    {
        $validFile = new LocalFile(static::$dir . 'recursive2/single.level.works');
        $validFile->makeDirectory();
        file_put_contents($validFile, 'some things');

        $subFolderFile = new LocalFile(static::$dir . 'recursive2/multiple/level.does.work');
        $subFolderFile->makeDirectory();
        file_put_contents($subFolderFile, 'other things');

        $collection = new FileNodeCollection();
        $this->populator->addFilesFromDirectory(
            $collection,
            $validFile->getDirectory(),
            function ($path) {
                return new LocalFile($path);
            },
            true
        );

        static::assertEquals(2, $collection->count());

        static::assertEquals(
            sort(array_map(function ($file) {
                return $file->getFilename();
            }, [$validFile, $subFolderFile])),
            sort($collection->map(function ($file) {
                return $file->getFilename();
            }))
        );
    }
}
