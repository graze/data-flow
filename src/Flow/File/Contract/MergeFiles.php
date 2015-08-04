<?php

namespace Graze\DataFlow\Flow\File\Contract;

use DirectoryIterator;
use Graze\DataFlow\Flow\File\MakeDirectory;
use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeCollectionInterface;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Utility\GetOption;
use Graze\DataFlow\Utility\Process\ProcessFactoryInterface;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MergeFiles extends Flow implements ExtensionInterface, FileContractorInterface
{
    use GetOption;

    /**
     * @var ProcessFactoryInterface
     */
    protected $processFactory;

    /**
     * @param ProcessFactoryInterface $processFactory
     */
    public function __construct(ProcessFactoryInterface $processFactory)
    {
        MakeDirectory::aware();
        $this->processFactory = $processFactory;
    }

    /**
     * @param ExtensibleInterface $extensible
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $extensible, $method)
    {
        return (
            ($extensible instanceof FileNodeCollectionInterface) &&
            $this->canContract($extensible) &&
            ($method == 'merge')
        );
    }

    /**
     * @param FileNodeCollectionInterface $files
     * @return bool
     */
    public function canContract(FileNodeCollectionInterface $files)
    {
        foreach ($files->getIterator() as $file) {
            if (
                !($file instanceof LocalFile) ||
                !($file->exists()) ||
                ($file->getCompression() != CompressionType::NONE)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeCollectionInterface $files
     * @param array                       $options -filePath <string> File to output to
     *                                             -keepOldFiles <bool> (Default: true) Keep the old files after merging
     * @return FileNodeInterface
     */
    public function contract(FileNodeCollectionInterface $files, array $options = [])
    {
        $this->options = $options;
        $filePath = $this->getOption('filePath', null);

        if (is_null($filePath)) {
            throw new InvalidArgumentException("Option 'filePath' is not defined");
        }

        $file = new LocalFile($filePath);

        return $this->merge($files, $file, $options);
    }

    /**
     * Add files from a local Directory
     *
     * @extend Graze\DataFlow\Node\File\FileNodeCollectionInterface
     * @param FileNodeCollectionInterface $collection
     * @param LocalFile                   $file
     * @param array                       $options -keepOldFiles <bool> (Default: true) keep or delete the old files
     * @return FileNodeInterface
     */
    public function merge(
        FileNodeCollectionInterface $collection,
        LocalFile $file,
        $options = []
    ) {
        $this->options = $options;

        $filePaths = $collection->map(function (LocalFile $item) {
            return $item->getPath();
        });
        $cmd = sprintf(
            'cat %s > %s',
            implode(' ', $filePaths),
            $file->getPath()
        );

        $file->makeDirectory();

        $process = $this->processFactory->createProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption('keepOldFiles', true)) {
            $collection->map(function (LocalFile $item) {
                if ($item->exists()) {
                    $item->delete();
                }
                $count = iterator_count(new DirectoryIterator($item->getDirectory()));
                if ($count == 2) {
                    rmdir($item->getDirectory());
                }
            });
        }

        return $file;
    }
}
