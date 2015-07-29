<?php

namespace Graze\DataFlow\Flow\File\Modify;

use Graze\DataFlow\Flow\File\Modify\Exception\CopyFailedException;
use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Utility\GetOption;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;

class Copy extends Flow implements ExtensionInterface, FileModifierInterface
{
    use GetOption;

    /**
     * Modify the file
     *
     * @param FileNodeInterface $file
     * @param array             $options -outputFilePath <string> (Optional) Output Path, if not specified the input
     *                                   filename with -copy on the end will be used
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        $this->options = $options;
        $outputFilePath = $this->getOption('outputFilePath', null);

        if (!($file instanceof LocalFile)) {
            throw new InvalidArgumentException("Supplied $file is not a LocalFile");
        }

        if ($outputFilePath) {
            $outputFile = $file->getClone()->setFilePath($outputFilePath);

            return $this->copy($file, $outputFile);
        } else {
            return $this->copy($file);
        }
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile $file
     * @param LocalFile $outputFile (Optional) if not specified a file with -copy on the end will be created
     * @return LocalFile
     * @throws CopyFailedException
     */
    public function copy(LocalFile $file, LocalFile $outputFile = null)
    {
        if (is_null($outputFile)) {
            $outputFile = $file->getClone()
                               ->setFilePath($file->getFilePath() . '.copy');
        }

        if (!@copy($file->getFilePath(), $outputFile->getFilePath())) {
            $lastError = error_get_last();
            throw new CopyFailedException($file, $outputFile, $lastError['message']);
        }

        return $outputFile;
    }

    /**
     * @param ExtensibleInterface $extensible
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $extensible, $method)
    {
        return (($extensible instanceof LocalFile) &&
            ($this->canModify($extensible)) &&
            ($method == 'copy'));
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
}
