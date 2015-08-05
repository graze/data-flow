<?php

namespace Graze\DataFlow\Flow\File\Modify\Compression;

use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Zip extends Flow implements ExtensionInterface, CompressorInterface
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
     * Compress a file and return the new file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     * @return FileNodeInterface
     */
    public function compress(FileNodeInterface $node, array $options = [])
    {
        if (!($node instanceof LocalFile)) {
            throw new InvalidArgumentException("Node: $node should be a LocalFile");
        }

        return $this->zip($node, $options);
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     *
     * @param LocalFile $file
     * @param array     $options -keepOldFile <bool> (Default: true)
     * @return LocalFile
     */
    public function zip(LocalFile $file, array $options = [])
    {
        $pathInfo = pathinfo($file->getPath());

        if (!$file->exists()) {
            throw new InvalidArgumentException("The file: $file does not exist");
        }

        $outputFile = $file->getClone()
                           ->setPath($pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.zip')
                           ->setCompression(CompressionType::ZIP);

        $cmd = "zip {$outputFile->getPath()} {$file->getPath()}";

        // @todo Logging

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$outputFile->exists() || exec("wc -c < {$outputFile->getPath()}") == 0) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption($options, 'keepOldFile', true)) {
            $file->delete();
        }

        return $outputFile;
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

    /**
     * Decompress a file and return the decompressed file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     * @return FileNodeInterface
     */
    public function decompress(FileNodeInterface $node, array $options = [])
    {
        if (!($node instanceof LocalFile)) {
            throw new InvalidArgumentException("Node: $node should be a LocalFile");
        }
        return $this->unzip($node, $options);
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     *
     * @param LocalFile $file
     * @param array     $options
     * @return FileNodeInterface
     */
    public function unzip(LocalFile $file, array $options = [])
    {
        $pathInfo = pathinfo($file->getPath());

        if (!$file->exists()) {
            throw new InvalidArgumentException("The file: $file does not exist");
        }

        $outputFile = $file->getClone()
                           ->setPath($pathInfo['dirname'] . '/' . $pathInfo['filename'])
                           ->setCompression(CompressionType::NONE);

        $cmd = "unzip -p {$file->getPath()} > {$outputFile->getPath()}";

        // @todo Logging

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$outputFile->exists() || exec("wc -c < {$outputFile->getPath()}") == 0) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption($options, 'keepOldFile', true)) {
            $file->delete();
        }

        return $outputFile;
    }
}
