<?php

namespace Graze\DataFlow\Flow\File\Modify;

use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Utility\ProcessFactory;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Tail extends Flow implements ExtensionInterface, FileModifierInterface
{
    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    /**
     * @param ProcessFactory $processFactory
     */
    public function __construct(ProcessFactory $processFactory)
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
        $lines = $this->getOption($options, 'lines', null);

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
        $postfix = $this->getOption($options, 'postfix', 'tail');
        if (strlen($postfix) > 0) {
            $postfix = '-' . $postfix;
        }

        $pathInfo = pathinfo($file->getFilePath());
        $outputFileName = $pathInfo['filename'] . $postfix . '.' . $pathInfo['extension'];
        $outputFilePath = $pathInfo['dirname'] . '/' . $outputFileName;

        $output = $file->getClone()
                       ->setFilePath($outputFilePath);

        $cmd = sprintf('tail -n %s %s > %s', $lines, $file->getFilePath(), $output->getFilePath());

        $process = $this->processFactory->createProcess($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$output->exists()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption($options, 'keepOldFile', true)) {
            unlink($file->getFilePath());
        }

        return $output;
    }
}
