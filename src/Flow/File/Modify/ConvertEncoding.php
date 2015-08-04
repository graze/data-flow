<?php

namespace Graze\DataFlow\Flow\File\Modify;

use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Utility\GetOption;
use Graze\DataFlow\Utility\Process\ProcessFactoryInterface;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
class ConvertEncoding extends Flow implements ExtensionInterface
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
        $this->processFactory = $processFactory;
    }

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
        $this->options = $options;

        $pathInfo = pathinfo($file->getPath());

        $outputFileName = sprintf(
            '%s-%s.%s',
            $pathInfo['filename'],
            $this->getOption('postfix', $toEncoding),
            $pathInfo['extension']
        );

        $output = $file->getClone()
                       ->setPath($pathInfo['dirname'] . '/' . $outputFileName)
                       ->setEncoding($toEncoding);

        $cmd = "iconv " .
            ($file->getEncoding() ? "--from-code={$file->getEncoding()} " : '') .
            "--to-code={$toEncoding} " .
            "{$file->getPath()} " .
            "> {$output->getPath()}";

        $process = $this->processFactory->createProcess($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$output->exists()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption('keepOldFile', true)) {
            $file->delete();
        }

        return $output;
    }
}
