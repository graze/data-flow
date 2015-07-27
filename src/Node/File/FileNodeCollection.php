<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataFlow\Node\DataNodeCollection;
use InvalidArgumentException;

/**
 * Class FileNodeCollection
 *
 * @package Graze\Dataflow\Node\File
 */
class FileNodeCollection extends DataNodeCollection implements FileNodeCollectionInterface
{
    /**
     * For a given set of files, return any common prefix (i.e. directory, s3 key)
     *
     * @return string|null
     */
    public function getCommonPrefix()
    {
        if ($this->count() == 0) {
            return null;
        }

        $commonPath = $this->reduce(function ($commonPath, FileNodeInterface $file) {
            if (is_null($commonPath)) {
                return $file->getFilePath();
            }
            return $this->getCommonPrefixString($commonPath, $file->getFilePath());
        });

        return (strlen($commonPath) > 0) ? $commonPath : null;
    }

    /**
     * @param string $left
     * @param string $right
     * @return string
     */
    private function getCommonPrefixString($left, $right)
    {
        for ($i = 1; $i < strlen($left); $i++) {
            if (substr_compare($left, $right, 0, $i) !== 0) {
                return substr($left, 0, $i - 1);
            }
        }
        return substr($left, 0, $i);
    }

    /**
     * {@inheritdoc}
     */
    public function add($value)
    {
        if (!($value instanceof FileNodeInterface)) {
            throw new InvalidArgumentException("The specified value does not implement FileNodeInterface");
        }
        return parent::add($value);
    }
}
