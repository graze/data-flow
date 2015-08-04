<?php

namespace Graze\DataFlow\Test\Fuctional\Flow\File\Modify;

use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use Graze\DataFlow\Flow\File\Modify\Compression\Gzip;
use Graze\DataFlow\Flow\File\Modify\Copy;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

class CopyTest extends FileTestCase
{
    public function setUp()
    {
        Gzip::aware();
    }
    public function testCopyCreatesADuplicateFile()
    {
        $localFile = new LocalFile(static::$dir . 'copy_orig.test');
        $localFile->put('some random text');

        $newFile = $localFile->copy($localFile->getPath() . '.copy');

        static::assertTrue($newFile->exists());
        static::assertEquals($localFile->getPath() . '.copy', $newFile->getPath());
        static::assertEquals($localFile->getContents(), $newFile->getContents());
    }

    public function testCopyCopiesAttributes()
    {
        $localFile = (new LocalFile(static::$dir . 'copy_attributes.text'))
            ->setEncoding('ascii');
        $localFile->put('some ascii text');

        $newFile = $localFile->copy($localFile->getPath() . '.copy');

        static::assertEquals('ascii', $newFile->getEncoding());

        $gzipped = $newFile->gzip();

        static::assertEquals(CompressionType::GZIP, $gzipped->getCompression());

        $gzipCopy = $gzipped->copy($gzipped->getPath() . '.copy');

        static::assertEquals($gzipped->getCompression(), $gzipCopy->getCompression());
    }

    public function testCopyAppendsCopyWhenNoPathIsSpecified()
    {
        $localFile = new LocalFile(static::$dir . 'copy_default_append.text');
        $localFile->put('some random text');

        $newFile = $localFile->copy();

        static::assertEquals($localFile->getPath() . '-copy', $newFile);
    }

    public function testWhenCopyFailsItRaisesAnException()
    {
        $localFile = new LocalFile(static::$dir . 'copy_failed.text');
        $localFile->put('some ascii text');

        $newPath = '/not/a/real/path/' . $localFile->getFilename();

        static::setExpectedException(
            'Graze\DataFlow\Flow\File\Modify\Exception\CopyFailedException',
            "Failed to copy file from: '$localFile' to '$newPath'. copy(/not/a/real/path/copy_failed.text): failed to open stream: No such file or directory"
        );

        $localFile->copy($newPath);
    }
}
