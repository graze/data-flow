<?php

namespace Graze\DataFlow\Flow\File\Modify;

use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Utility\GetOption;
use Graze\DataFlow\Utility\Process\ProcessFactoryInterface;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Tail extends Flow implements ExtensionInterface, FileModifierInterface
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
     * @param ExtensibleInterface $extensible
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $extensible, $method)
    {
        return (($extensible instanceof FileNodeInterface) &&
            ($method == 'tail') &&
            ($this->canModify($extensible))
        );
    }

    /**
     * Can this file be modified by this modifier
     *
     * @param FileNodeInterface $file
     * @return bool
     */
    public function canModify(FileNodeInterface $file)
    {
        return (($file instanceof LocalFile) &&
            ($file->exists()));
    }

    /**
     * Modify the file
     *
     * @param FileNodeInterface $file
     * @param array             $options List of options:
     *                                   -lines <string> Number of lines to tail (accepts +/- modifiers)
     *                                   -postfix <string> (Default: replace) Set this to blank to replace inline
     *                                   -keepOldFile <bool> (Default: true)
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        $this->options = $options;
        $lines = $this->getOption('lines', null);

        if (is_null($lines)) {
            throw new InvalidArgumentException("Missing option: 'lines'");
        }

        unset($options['lines']);

        if (!($file instanceof LocalFile)) {
            throw new InvalidArgumentException("Supplied: $file is not a LocalFile");
        }

        return $this->tail($file, $lines, $options);
    }

    /**
     * Tail a file
     *
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile $file
     * @param string    $lines         Number of lines to tail (accepts +/- modifiers)
     * @param array     $options       List of options:
     *                                 -postfix <string> (Default: tail)
     *                                 -keepOldFile <bool> (Default: true)
     * @throws ProcessFailedException
     * @return LocalFile
     */
    public function tail(LocalFile $file, $lines, array $options = [])
    {
        $this->options = $options;

        $postfix = $this->getOption('postfix', 'tail');
        if (strlen($postfix) > 0) {
            $postfix = '-' . $postfix;
        }

        $pathInfo = pathinfo($file->getPath());
        $outputFileName = $pathInfo['filename'] . $postfix . '.' . $pathInfo['extension'];
        $outputFilePath = $pathInfo['dirname'] . '/' . $outputFileName;

        $output = $file->getClone()
                       ->setPath($outputFilePath);

        $cmd = sprintf('tail -n %s %s > %s', $lines, $file->getPath(), $output->getPath());

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
