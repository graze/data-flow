<?php
/**
 * This file is part of graze/data-flow
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/data-flow/blob/master/LICENSE.md
 * @link    https://github.com/graze/data-flow
 */

namespace Graze\DataFlow\Test;

use Graze\DataFile\Node\FileNode;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

abstract class MemoryFileTestCase extends TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    protected function setUp()
    {
        $this->filesystem = new Filesystem(new MemoryAdapter());
    }

    /**
     * @param string      $path
     * @param string|null $contents
     *
     * @return FileNode
     */
    protected function makeFile($path, $contents = null)
    {
        $file = new FileNode($this->filesystem, $path);
        if (!is_null($contents)) {
            $file->write($contents);
        }
        return $file;
    }
}
