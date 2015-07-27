<?php

namespace Graze\DataFlow\Flow\File\Expand;

use Graze\DataFlow\Flow\File\Collection\AddFilesFromDirectory;
use Graze\DataFlow\Flow\File\Exception\UnknownVersionException;
use Graze\DataFlow\Flow\File\MakeDirectory;
use Graze\DataFlow\Flow\File\Modify\Copy;
use Graze\DataFlow\Flow\File\Modify\ReplaceText;
use Graze\DataFlow\Flow\File\Modify\Tail;
use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeCollection;
use Graze\DataFlow\Node\File\FileNodeCollectionInterface;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Utility\ProcessFactory;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SplitFile extends Flow implements ExtensionInterface, FileExpanderInterface
{
    const PART_PREFIX = 'part_';

    const TYPE_PART  = 'part';
    const TYPE_LINES = 'lines';

    const VERSION_GNU  = 'GNU';
    const VERSION_UNIX = 'UNIX';

    /**
     * @var array
     */
    protected $options;

    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    public function __construct(ProcessFactory $processFactory)
    {
        MakeDirectory::aware();
        AddFilesFromDirectory::aware();
        ReplaceText::aware();
        Tail::aware();
        Copy::aware();

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
            ($extensible instanceof LocalFile) &&
            ($this->canExpand($extensible)) &&
            (
                ($method == 'splitIntoParts') ||
                ($method == 'splitByLines')
            )
        );
    }

    /**
     * @param FileNodeInterface $file
     * @return bool
     */
    public function canExpand(FileNodeInterface $file)
    {
        return (
            ($file instanceof LocalFile) &&
            ($file->exists())
        );
    }

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeInterface $file
     * @param array             $options -numParts <int> Number of parts to split into
     *                                   -byLines <int> Number of lines for each file
     *                                   -postfix <string> (Default: <date - YYYYMMDD_HHMMSS>
     *                                   -keepOldFile <bool> (Default: true)
     * @return FileNodeCollectionInterface
     */
    public function expand(FileNodeInterface $file, array $options = [])
    {
        $this->options = $options;
        $numParts = $this->getOption('numParts', null);
        $byLines = $this->getOption('byLines', null);

        if (is_null($numParts) && is_null($byLines)) {
            throw new InvalidArgumentException("Either 'numParts' or 'byLines' should be specified in the options");
        }

        if (!($file instanceof LocalFile)) {
            throw new InvalidArgumentException("The specified $file is not a LocalFile");
        }

        if (!is_null($numParts)) {
            return $this->split($file, static::TYPE_PART, $numParts);
        } else {
            return $this->split($file, static::TYPE_LINES, $byLines);
        }
    }

    /**
     * Get an option value
     *
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    private function getOption($name, $default)
    {
        return (isset($this->options[$name])) ? $this->options[$name] : $default;
    }

    /**
     * @param LocalFile $file
     * @param string    $type Type of split SplitFile::TYPE_
     * @param mixed     $option
     * @return FileNodeCollectionInterface
     */
    private function split(LocalFile $file, $type, $option)
    {
        $folder = $this->prepareFolder($file);
        $tmpFile = $this->prepareFile($file);
        $extension = pathinfo($file->getFilePath(), PATHINFO_EXTENSION);
        $prefix = $folder->getDirectory() . static::PART_PREFIX;

        $cmd = $this->getCommand(
            $this->getSplitVersion(),
            $type,
            $option,
            $extension,
            $tmpFile->getFilePath(),
            $prefix
        );

        $process = $this->processFactory->createProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $collection = $this->postProcessFolder($folder->getDirectory());

        // tidy up
        unlink($tmpFile->getFilePath());
        if (!$this->getOption('keepOldFile', true)) {
            unlink($file->getFilePath());
        }

        return $collection;
    }

    /**
     * @param LocalFile $file
     * @return LocalFile
     */
    private function prepareFolder(LocalFile $file)
    {
        $postfix = $this->getOption('postfix', strftime('%Y%m%d_%H%M%S'));

        $pathInfo = pathinfo($file->getFilePath());

        $folder = new LocalFile($pathInfo['dirname'] . '/' . $pathInfo['filename'] . '-' . $postfix . '/part');
        $folder->makeDirectory();

        return $folder;
    }

    /**
     * This prepares the file to be split (removing any lines and replacing specific new lines so they don't get split
     *
     * @param LocalFile $file
     * @return LocalFile
     */
    private function prepareFile(LocalFile $file)
    {
        $ignoreLines = $this->getOption('ignoreLines', 0);
        $tmpFile = ($ignoreLines > 0) ?
            $file->tail('+' . ($ignoreLines + 1)) :
            $file->copy();

        return $tmpFile->replaceText(
            [
                "(\\\\\\\\++)(n|r)",
                "(?<!\\\\)\\\\\\n",
                "(?<!\\\\)\\\\\\r"
            ],
            [
                "\\1\\1\\2",
                "\\\\\\\\n",
                "\\\\\\\\r"
            ]
        );
    }

    /**
     * @param string $version one of SplitFile::VERSION_
     * @param string $type    one of SplitFile::TYPE_
     * @param int    $option
     * @param string $extension
     * @param string $path
     * @param string $prefix
     * @return string
     * @throws UnknownVersionException
     */
    private function getCommand(
        $version,
        $type,
        $option,
        $extension,
        $path,
        $prefix
    ) {
        switch ($version) {
            case static::VERSION_GNU:
                switch ($type) {
                    case static::TYPE_PART:
                        $cmd = sprintf(
                            "split -n l/%d -d -e --additional-suffix=.%s %s %s",
                            $option,
                            $extension,
                            $path,
                            $prefix);
                        break;
                    case static::TYPE_LINES:
                        $cmd = sprintf(
                            "split -l %d -d -e --additional-suffix=.%s %s %s",
                            $option,
                            $extension,
                            $path,
                            $prefix);
                        break;
                    default:
                        throw new InvalidArgumentException("Unknown Type: $type");
                }
                break;
            case static::VERSION_UNIX:
                switch ($type) {
                    case static::TYPE_PART:
                        $totalLines = exec('wc -l < ' . $path);
                        $option = ceil($totalLines / $option);
                    // purposefully going to next case here
                    case static::TYPE_LINES:
                        $cmd = sprintf(
                            "split -l %d %s %s",
                            $option,
                            $path,
                            $prefix);
                        break;
                    default:
                        throw new InvalidArgumentException("Unknown Type: $type");
                }
                break;
            default:
                throw new UnknownVersionException("The version of split could not be determined from: $version");
        }
        return $cmd;
    }

    /**
     * @return string GNU or UNIX
     */
    private function getSplitVersion()
    {
        $cmd = 'split --version';
        $process = $this->processFactory->createProcess($cmd);
        $process->run();
        if (stripos($process->getOutput(), 'split (GNU coreutils)') !== false) {
            return static::VERSION_GNU;
        } else {
            return static::VERSION_UNIX;
        }
    }

    /**
     * Create and return a FileNodeCollection with all the files in them with new line fixes
     *
     * @param string $directory
     * @return FileNodeCollectionInterface
     */
    private function postProcessFolder($directory)
    {
        // create file collection out of newly created files
        $collection = new FileNodeCollection();
        $collection->addFilesFromDirectory(
            $directory,
            function ($file) {
                return new LocalFile($file);
            }
        )
                   ->map(function (FileNodeInterface $item) {
                       return $item->replaceText(
                           [
                               "(?<!\\\\\\\\)\\\\\\\\n",
                               "(?<!\\\\\\\\)\\\\\\\\r",
                               "(\\\\\\\\)+(n|r)",
                           ],
                           [
                               "\\\\\\n",
                               "\\\\\\r",
                               "\\1\\2",
                           ]
                       );
                   });
        return $collection;
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile $file
     * @param int       $numParts Number of parts to split the file into
     * @param array     $options  List of optional options:
     *                            -ignoreLines <int> (Default: 0) Number of lines to ignore when splitting
     *                            -postfix <string> (Default: <date - YYYYMMDD_HHMMSS>
     *                            -keepOldFile <bool> (Default: true)
     * @return FileNodeCollectionInterface
     */
    public function splitIntoParts(LocalFile $file, $numParts, array $options = [])
    {
        $this->options = $options;
        return $this->split($file, static::TYPE_PART, $numParts);
    }

    /**
     * @extend Graze\DataFlow\Node\File\LocalFile
     * @param LocalFile $file
     * @param int       $lines    Number of lines to split into a a new file at
     * @param array     $options  List of optional options:
     *                            -postfix <string> (Default: <date - YYYYMMDD_HHMMSS>
     *                            -keepOldFile <bool> (Default: true)
     * @return FileNodeCollectionInterface
     */
    public function splitByLines(LocalFile $file, $lines, array $options = [])
    {
        $this->options = $options;
        return $this->split($file, static::TYPE_LINES, $lines);
    }
}
