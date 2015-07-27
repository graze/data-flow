<?php

namespace Graze\DataFlow\Test\Functional\Flow\File\Modify;

use Graze\DataFlow\Flow\File\MakeDirectory;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

class MakeDirectoryTest extends FileTestCase
{
    /**
     * @var MakeDirectory
     */
    protected $maker;

    public function setUp()
    {
        $this->maker = new MakeDirectory();
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\Extensible\ExtensionInterface', $this->maker);
    }

    public function testCanExtendAcceptsLocalFile()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');

        static::assertTrue($this->maker->canExtend($localFile, 'makeDirectory'));

        $randomThing = m::mock('Graze\DataFlow\Node\File\FileNodeInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->maker->canExtend($randomThing, 'makeDirectory'));
    }

    public function testCanExtendOnlyAcceptsTheCopyMethod()
    {
        $localFile = m::mock('Graze\DataFlow\Node\File\LocalFile');

        static::assertTrue($this->maker->canExtend($localFile, 'makeDirectory'));
        static::assertFalse($this->maker->canExtend($localFile, 'somethingElse'));
    }

    public function testCanMakeDirectory()
    {
        $file = new LocalFile(static::$dir . 'test/file');

        static::assertFalse(file_exists($file->getDirectory()));

        $retFile = $this->maker->makeDirectory($file);

        static::assertTrue(file_exists($file->getDirectory()));
        static::assertSame($file, $retFile);
    }

    public function testCanMakeDirectorWithSpecificUMode()
    {
        $file = new LocalFile(static::$dir . 'umode_test/file');

        static::assertFalse(file_exists($file->getDirectory()));

        $retFile = $this->maker->makeDirectory($file, 0744);

        static::assertEquals(0744, fileperms($file->getDirectory()) & 0777);
        static::assertSame($retFile, $file);
    }

    public function testCanCallMakeDirectoryWithAnExistingFolder()
    {
        $file = new LocalFile(static::$dir .'no_dir_file');

        static::assertTrue(file_exists($file->getDirectory()));

        $retFile = $this->maker->makeDirectory($file);

        static::assertSame($retFile, $file);
    }

    public function testCreatingADirectoryWithoutPermissionThrowsAnException()
    {
        $validDirectory = new LocalFile(static::$dir . 'valid/dir.test');

        $this->maker->makeDirectory($validDirectory, 0444);
        static::assertTrue(file_exists($validDirectory->getDirectory()));
        static::assertEquals(0444, fileperms($validDirectory->getDirectory()) & 0777);

        $invalidDirectory = new LocalFile(static::$dir . 'valid/invalid/dir.test');

        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Exception\MakeDirectoryFailedException',
            "Failed to create directory: '{$invalidDirectory->getDirectory()}'. mkdir(): Permission denied"
        );

        $this->maker->makeDirectory($invalidDirectory);
    }

    public function testCanMakeDirectoryUsingExtension()
    {
        $file = new LocalFile(static::$dir . 'extensionTest/file');

        static::assertFalse(file_exists($file->getDirectory()));

        $retFile = $file->makeDirectory();

        static::assertTrue(file_exists($file->getDirectory()));
        static::assertSame($file, $retFile);
    }
}
