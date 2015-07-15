<?php

namespace Graze\DataFlow\Flow\File;

use Graze\DataFlow\Node\File\LocalFile;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ReplaceText implements ExtensionInterface
{
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

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile       $file
     * @param string|string[] $fromText
     * @param string|string[] $toText
     * @param array           $options List of options:
     *                                 -postifx <string> (Default: replace) Set this to blank to replace inline
     *                                 -keepOldFile <bool> (Default: true)
     * @throws InvalidArgumentException
     * @throws ProcessFailedException
     * @return LocalFile
     */
    public function replaceText(LocalFile $file, $fromText, $toText, array $options = [])
    {
        $postfix = $this->getOption($options, 'postfix', 'replace');
        if (strlen($postfix) > 0) {
            $postfix = '-' . $postfix;
        }

        $pathInfo = pathinfo($file->getFilePath());
        $outputFileName = $pathInfo['filename'] . $postfix . '.' . $pathInfo['extension'];
        $outputFilePath = $pathInfo['dirname'] . '/' . $outputFileName;

        $output = new LocalFile($outputFilePath, ['encoding' => $file->getEncoding()]);

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
     * @param ExtensibleInterface $extensible
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $extensible, $method)
    {
        return (($extensible instanceof LocalFile) &&
            ($method == 'replaceText') &&
            ($extensible->exists())
        );
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
