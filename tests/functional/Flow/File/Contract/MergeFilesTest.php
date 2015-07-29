<?php

namespace Graze\DataFlow\Test\Functional\Flow\File\Contract;

use Graze\DataFlow\Flow\File\Contract\MergeFiles;
use Graze\DataFlow\Flow\File\MakeDirectory;
use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use Graze\DataFlow\Node\File\FileNodeCollection;
use Graze\DataFlow\Node\File\FileNodeCollectionInterface;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\File\FileTestCase;
use Graze\DataFlow\Utility\ProcessFactory;
use Mockery as m;

class MergeFilesTest extends FileTestCase
{
    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    /**
     * @var MergeFiles
     */
    protected $merge;

    public function setUp()
    {
        MakeDirectory::aware();
        $this->processFactory = m::mock('Graze\DataFlow\Utility\ProcessFactory')->makePartial();
        $this->merge = new MergeFiles($this->processFactory);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\Extensible\ExtensionInterface', $this->merge);
        static::assertInstanceOf('Graze\DataFlow\Flow\File\Contract\FileContractorInterface', $this->merge);
    }

    public function testCanExtendAcceptsFileNodeCollectionInterface()
    {
        $collection = m::mock('Graze\DataFlow\Node\File\FileNodeCollectionInterface');
        $collection->shouldReceive('getIterator')
                   ->andReturn([]);

        static::assertTrue($this->merge->canExtend($collection, 'merge'));

        $randomThing = m::mock('Graze\DataFlow\Node\DataNodeCollectionInterface',
            'Graze\Extensible\ExtensibleInterface');

        static::assertFalse($this->merge->canExtend($randomThing, 'merge'));
    }

    public function testCanExtendOnlyAcceptsLocalFiles()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file1);

        static::assertTrue($this->merge->canExtend($collection, 'merge'));

        $file2 = m::mock('Graze\DataFlow\Node\File\FileNodeInterface');
        $file2->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file2);

        static::assertFalse($this->merge->canExtend($collection, 'merge'));
    }

    public function testCanExtendOnlyWithFilesThatExist()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file1);

        static::assertTrue($this->merge->canExtend($collection, 'merge'));

        $file2 = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $file2->shouldReceive('exists')
              ->andReturn(false);
        $file2->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file2);

        static::assertFalse($this->merge->canExtend($collection, 'merge'));
    }

    public function testCanExtendOnlyUncompressedFiles()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file1);

        static::assertTrue($this->merge->canExtend($collection, 'merge'));

        $file2 = m::mock('Graze\DataFlow\Node\File\LocalFile');
        $file2->shouldReceive('exists')
              ->andReturn(true);
        $file2->shouldReceive('getCompression')
              ->andReturn(CompressionType::GZIP);
        $collection->add($file2);

        static::assertFalse($this->merge->canExtend($collection, 'merge'));
    }

    public function testCanExtendOnlyAcceptsTheMergeMethod()
    {
        $collection = m::mock('Graze\DataFlow\Node\File\FileNodeCollectionInterface');
        $collection->shouldReceive('getIterator')
                   ->andReturn([]);

        static::assertTrue($this->merge->canExtend($collection, 'merge'));
        static::assertFalse($this->merge->canExtend($collection, 'somethingelse'));
    }

    public function testSimpleMergeFiles()
    {
        $collection = $this->createCollection('simple.merge/', 3);

        $outputFile = new LocalFile(static::$dir . 'simple.merge.output');

        $file = $this->merge->merge($collection, $outputFile);

        static::assertSame($file, $outputFile);
        static::assertEquals(
            [
                "File 1 Line 1\n",
                "File 1 Line 2\n",
                "File 1 Line 3\n",
                "File 2 Line 1\n",
                "File 2 Line 2\n",
                "File 2 Line 3\n",
                "File 3 Line 1\n",
                "File 3 Line 2\n",
                "File 3 Line 3\n",
            ],
            $file->getContents()
        );

        $exists = $collection->filter(function (FileNodeInterface $item) {
            return $item->exists();
        });

        static::assertCount(3, $exists);
    }

    /**
     * @param string $rootDir
     * @param int    $numFiles
     * @return FileNodeCollectionInterface
     */
    private function createCollection($rootDir, $numFiles)
    {
        $collection = new FileNodeCollection();
        for ($i = 1; $i <= $numFiles; $i++) {
            $file = new LocalFile(static::$dir . $rootDir . 'part_' . $i);
            $file->makeDirectory();
            file_put_contents($file->getFilePath(), "File $i Line 1\nFile $i Line 2\nFile $i Line 3\n");
            $collection->add($file);
        }
        return $collection;
    }

    public function testCallingMergeWithKeepOldFilesAsFalseDeletesAllTheFilesInTheCollection()
    {
        $collection = $this->createCollection('simple.merge.delete/', 3);

        $outputFile = new LocalFile(static::$dir . 'simple.merge.delete.output');

        $file = $this->merge->merge($collection, $outputFile, ['keepOldFiles' => false]);

        static::assertSame($file, $outputFile);
        static::assertEquals(
            [
                "File 1 Line 1\n",
                "File 1 Line 2\n",
                "File 1 Line 3\n",
                "File 2 Line 1\n",
                "File 2 Line 2\n",
                "File 2 Line 3\n",
                "File 3 Line 1\n",
                "File 3 Line 2\n",
                "File 3 Line 3\n",
            ],
            $file->getContents()
        );

        $exists = $collection->filter(function (FileNodeInterface $item) {
            return $item->exists();
        });

        static::assertCount(0, $exists);
    }

    public function testProcessFailedThrowException()
    {
        $process = m::mock('Symfony\Component\Process\Process')->makePartial();
        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $process->shouldReceive('isSuccessful')->andReturn(false);

        // set exception as no guarantee process will run on local system
        static::setExpectedException('Symfony\Component\Process\Exception\ProcessFailedException');

        $collection = $this->createCollection('simple.merge/', 3);

        $outputFile = new LocalFile(static::$dir . 'simple.merge.output');

        $this->merge->merge($collection, $outputFile);
    }

    public function testCallingContractWillCallMerge()
    {
        $collection = $this->createCollection('simple.contract/', 3);

        $file = $this->merge->contract(
            $collection,
            [
                'filePath'     => static::$dir . 'simple.contract.output',
                'keepOldFiles' => true
            ]
        );

        static::assertSame(static::$dir . 'simple.contract.output', $file->getFilePath());
        static::assertEquals(
            [
                "File 1 Line 1\n",
                "File 1 Line 2\n",
                "File 1 Line 3\n",
                "File 2 Line 1\n",
                "File 2 Line 2\n",
                "File 2 Line 3\n",
                "File 3 Line 1\n",
                "File 3 Line 2\n",
                "File 3 Line 3\n",
            ],
            $file->getContents()
        );

        $exists = $collection->filter(function (FileNodeInterface $item) {
            return $item->exists();
        });

        static::assertCount(3, $exists);
    }

    public function testCallingContractWillPassThroughOptions()
    {
        $collection = $this->createCollection('simple.contract.pass.through/', 3);

        $file = $this->merge->contract(
            $collection,
            [
                'filePath'     => static::$dir . 'simple.contract.pass.through.output',
                'keepOldFiles' => true
            ]
        );

        static::assertEquals(
            [
                "File 1 Line 1\n",
                "File 1 Line 2\n",
                "File 1 Line 3\n",
                "File 2 Line 1\n",
                "File 2 Line 2\n",
                "File 2 Line 3\n",
                "File 3 Line 1\n",
                "File 3 Line 2\n",
                "File 3 Line 3\n",
            ],
            $file->getContents()
        );

        $exists = $collection->filter(function (FileNodeInterface $item) {
            return $item->exists();
        });

        static::assertCount(3, $exists);
    }

    public function testCallingContractWithNoOptionsWillThrowException()
    {
        $collection = $this->createCollection('simple.contract.fail/', 3);

        static::setExpectedException(
            'InvalidArgumentException',
            "Option 'filePath' is not defined"
        );

        $this->merge->contract($collection);
    }
}
