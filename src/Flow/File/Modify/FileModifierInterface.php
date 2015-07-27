<?php

namespace Graze\DataFlow\Flow\File\Modify;

use Graze\DataFlow\Node\File\FileNodeInterface;

interface FileModifierInterface
{
    /**
     * Can this file be modified by this modifier
     *
     * @param FileNodeInterface $file
     * @return bool
     */
    public function canModify(FileNodeInterface $file);

    /**
     * Modify the file
     *
     * @param FileNodeInterface $file
     * @param array             $options
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = []);
}
