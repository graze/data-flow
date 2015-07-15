<?php

namespace Graze\DataFlow\Flow\File\Compression;

use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Zip implements ExtensionInterface, CompressorInterface
{
    /**
     * Determine if this object can act upon the supplied node
     *
     * @param ExtensibleInterface $node
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $node, $method)
    {
        if (!($node instanceof LocalFile)) {
            return false;
        }

        return ((in_array($method, ['zip', 'unzip'])) &&
            (($method == 'zip') && ($node->getCompression() == CompressionType::NONE)) ||
            (($method == 'unzip') && ($node->getCompression() == CompressionType::ZIP)));
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     *
     * @param FileNodeInterface $file
     * @param array             $options -keepOldFile <bool> (Default: true)
     * @return FileNodeInterface
     */
    public function zip(FileNodeInterface $file, array $options = [])
    {
        $pathInfo = pathinfo($file->getFilePath());

        if (!$file->exists()) {
            throw new InvalidArgumentException("The file: $file does not exist");
        }

        $outputFile = new LocalFile(
            $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.zip',
            [
                'compression' => CompressionType::ZIP,
                'encoding' => $file->getEncoding()
            ]
        );

        $cmd = "zip {$outputFile->getFilePath()} {$file->getFilePath()}";

        // @todo Logging

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($outputFile->getFilePath()) || exec("wc -c < {$outputFile->getFilePath()}") == 0) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption($options, 'keepOldFile', true)) {
            unlink($file->getFilePath());
        }

        return $outputFile;
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     *
     * @param FileNodeInterface $file
     * @param array             $options
     * @return FileNodeInterface
     */
    public function unzip(FileNodeInterface $file, array $options = [])
    {
        $pathInfo = pathinfo($file->getFilePath());

        if (!$file->exists()) {
            throw new InvalidArgumentException("The file: $file does not exist");
        }

        $outputFile = new LocalFile(
            $pathInfo['dirname'] . '/' . $pathInfo['filename'],
            [
                'compression' => CompressionType::NONE,
                'encoding' => $file->getEncoding()
            ]
        );

        $cmd = "unzip -p {$file->getFilePath()} > {$outputFile->getFilePath()}";

        // @todo Logging

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($outputFile->getFilePath()) || exec("wc -c < {$outputFile->getFilePath()}") == 0) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption($options, 'keepOldFile', true)) {
            unlink($file->getFilePath());
        }

        return $outputFile;
    }

    /**
     * Compress a file and return the new file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     * @return FileNodeInterface
     */
    public function compress(FileNodeInterface $node, array $options = [])
    {
        return $this->zip($node, $options);
    }

    /**
     * Decompress a file and return the decompressed file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     * @return FileNodeInterface
     */
    public function decompress(FileNodeInterface $node, array $options = [])
    {
        return $this->unzip($node, $options);
    }

    /**
     * Get an option value
     *
     * @param array  $options
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    private function getOption($options, $name, $default)
    {
        return (isset($options[$name])) ? $options[$name] : $default;
    }
}
