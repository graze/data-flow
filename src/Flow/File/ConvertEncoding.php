<?php

namespace Graze\DataFlow\Flow\File;

use Graze\DataFlow\Node\File\LocalFile;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class ConvertEncoding
 *
 * Convert the Encoding of a file
 *
 * For a list of the supported encodings run:
 *
 * ```bash
 * iconv -l
 * ```
 *
 * @package Graze\DataFlow\Flow\File
 */
class ConvertEncoding implements ExtensionInterface
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
        return (($node instanceof LocalFile) &&
            ($method == 'toEncoding') &&
            ($node->exists()));
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile Only apply to local files
     *
     * @param LocalFile $file
     * @param string    $toEncoding           Encoding as defined by iconv
     * @param array     $options              -postfix <string> (Default: toEncoding)
     *                                        -keepOldFile <bool> (Default: true)
     * @return LocalFile
     */
    public function toEncoding(LocalFile $file, $toEncoding, array $options = [])
    {
        $pathInfo = pathinfo($file->getFilePath());

        $outputFileName = sprintf(
            '%s-%s.%s',
            $pathInfo['filename'],
            $this->getOption($options, 'postfix', $toEncoding),
            $pathInfo['extension']
        );

        $output = new LocalFile($pathInfo['dirname'] . '/' . $outputFileName);

        $cmd = "iconv " .
            ($file->getEncoding() ? "--from-code={$file->getEncoding()} " : '') .
            "--to-code={$toEncoding} " .
            "{$file->getFilePath()} " .
            "> {$output->getFilePath()}";

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$output->exists()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption($options, 'keepOldFile', true)) {
            unlink($file->getFilePath());
        }

        return $output;
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
