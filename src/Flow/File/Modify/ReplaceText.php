<?php

namespace Graze\DataFlow\Flow\File\Modify;

use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Utility\GetOption;
use Graze\DataFlow\Utility\ProcessFactory;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ReplaceText extends Flow implements ExtensionInterface, FileModifierInterface
{
    use GetOption;

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
        return (
            ($extensible instanceof FileNodeInterface) &&
            ($method == 'replaceText') &&
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
        return (
            ($file instanceof localFile) &&
            ($file->exists())
        );
    }

    /**
     * Modify the file
     *
     * @param FileNodeInterface $file
     * @param array             $options List of options:
     *                                   -fromText <string|array> Text to be replace
     *                                   -toText <string|array> Text to replace
     *                                   -postifx <string> (Default: replace) Set this to blank to replace inline
     *                                   -keepOldFile <bool> (Default: true)
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        $this->options = $options;
        $fromText = $this->getOption('fromText', null);
        $toText = $this->getOption('toText', null);

        if (is_null($fromText)) {
            throw new InvalidArgumentException("Missing option: 'fromText'");
        }
        if (is_null($toText)) {
            throw new InvalidArgumentException("Missing option: 'toText'");
        }

        unset($options['fromText']);
        unset($options['toText']);

        if (!($file instanceof LocalFile)) {
            throw new InvalidArgumentException("Supplied: $file is not a LocalFile");
        }

        return $this->replaceText($file, $fromText, $toText, $options);
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile       $file
     * @param string|string[] $fromText
     * @param string|string[] $toText
     * @param array           $options List of options:
     *                                 -postfix <string> (Default: replace) Set this to blank to replace inline
     *                                 -keepOldFile <bool> (Default: true)
     * @throws InvalidArgumentException
     * @throws ProcessFailedException
     * @return LocalFile
     */
    public function replaceText(LocalFile $file, $fromText, $toText, array $options = [])
    {
        $this->options = $options;
        $postfix = $this->getOption('postfix', 'replace');
        if (strlen($postfix) > 0) {
            $postfix = '-' . $postfix;
        }

        $pathInfo = pathinfo($file->getFilePath());
        $outputFileName = $pathInfo['filename'] . $postfix;
        if (isset($pathInfo['extension'])) {
            $outputFileName .= '.' . $pathInfo['extension'];
        }
        $outputFilePath = $pathInfo['dirname'] . '/' . $outputFileName;

        $output = $file->getClone()
                       ->setFilePath($outputFilePath);

        $replacementString = null;

        if (is_array($fromText)) {
            if (is_array($toText) &&
                count($fromText) == count($toText)
            ) {
                $sedStrings = [];
                for ($i = 0; $i < count($fromText); $i++) {
                    $sedStrings[] = $this->getReplacementCommand($fromText[$i], $toText[$i]);
                }
                $replacementString = implode(';', $sedStrings);
            } else {
                throw new InvalidArgumentException("Number of items in 'fromText' (" . count($fromText) . ") is different to 'toText' (" . count($toText) . ")");
            }
        } else {
            $replacementString = $this->getReplacementCommand($fromText, $toText);
        }

        if ($file->getFilename() == $output->getFilename()) {
            $cmd = sprintf(
                "perl -p -i -e '%s' %s",
                $replacementString,
                $file->getFilePath());
        } else {
            $cmd = sprintf(
                "perl -p -e '%s' %s > %s",
                $replacementString,
                $file->getFilePath(),
                $output->getFilePath());
        }

        $process = $this->processFactory->createProcess($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$output->exists()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption('keepOldFile', true)) {
            unlink($file->getFilePath());
        }

        return $output;
    }

    /**
     * Get the string replacement command for a single item
     *
     * @param $fromText
     * @param $toText
     * @return string
     */
    private function getReplacementCommand($fromText, $toText)
    {
        return sprintf('s/%s/%s/g',
            str_replace(["'", ";", "\\"], ["\\'", "\\;", "\\\\"], $fromText),
            str_replace(["'", ";", "\\"], ["\\'", "\\;", "\\\\"], $toText)
        );
    }
}
