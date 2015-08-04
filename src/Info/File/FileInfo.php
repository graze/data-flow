<?php

namespace Graze\DataFlow\Info\File;

use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Utility\Process\ProcessFactoryInterface;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FileInfo implements ExtensionInterface
{
    /**
     * @var ProcessFactoryInterface
     */
    protected $processFactory;

    /**
     * @param ProcessFactoryInterface $processFactory
     */
    public function __construct(ProcessFactoryInterface $processFactory)
    {
        $this->processFactory = $processFactory;
    }

    /**
     * @param ExtensibleInterface $extensible
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $extensible, $method)
    {
        return (($extensible instanceof LocalFile) &&
            (($method == 'findEncoding') || ($method == 'findCompression')) &&
            ($extensible->exists()));
    }

    /**
     * Find the Encoding of a specified file
     *
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile $file
     * @return null|string
     * @throws ProcessFailedException
     */
    public function findEncoding(LocalFile $file)
    {
        $cmd = "file --brief --uncompress --mime {$file->getPath()}";

        $process = $this->processFactory->createProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $result = $process->getOutput();
        if (preg_match('/charset=([^\s]+)/i', $result, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Find the compression of a specified file
     *
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile $file
     * @return string|null
     */
    public function findCompression(LocalFile $file)
    {
        $cmd = "file --brief --uncompress --mime {$file->getPath()}";

        $process = $this->processFactory->createProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $result = $process->getOutput();
        if (preg_match('/compressed-encoding=application\/(?:x-)?(.+?);/i', $result, $matches)) {
            if (in_array($matches[1], CompressionType::getCompressionTypes())) {
                return $matches[1];
            }
            return CompressionType::UNKNOWN;
        }
        return CompressionType::NONE;
    }
}
