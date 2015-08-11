<?php

namespace Graze\DataFlow\Test\Functional\Node\File\Source;

use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Node\File\Source\FileSource;
use Graze\DataFlow\Test\File\FileTestCase;
use Mockery as m;

class FileSourceTest extends FileTestCase
{
    public function testGetFilesCallsMatches()
    {
        $file = $this->createFile("basic_0.txt", 'some stuff');
        // create a bunch of files
        for ($i = 1; $i < 5; $i++) {
            $this->createFile("basic_$i.txt", 'some stuff');
        }

        $filter = m::mock('Graze\DataFlow\Utility\ArrayFilter\ArrayFilterInterface');

        $filter->shouldReceive('matches')->with(m::on(function ($metadata) {
            static::assertArrayHasKey('basename', $metadata);
            static::assertArrayHasKey('path', $metadata);
            static::assertArrayHasKey('timestamp', $metadata);
            static::assertArrayHasKey('path', $metadata);
            static::assertArrayHasKey('size', $metadata);
            static::assertArrayHasKey('dirname', $metadata);
            return true;
        }))->andReturn(true, false, true, false, true);

        $source = new FileSource($file->getFilesystem(), static::$dir, $filter);

        $files = $source->getFiles(false);

        static::assertCount(3, $files->getAll());
    }

    /**
     * @param $name
     * @param $contents
     *
     * @return LocalFile
     */
    private function createFile($name, $contents)
    {
        $file = new LocalFile(static::$dir . $name);
        $file->put($contents);
        return $file;
    }
}
