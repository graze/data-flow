<?php

namespace Graze\DataFlow\Flow\File\Compression;

use Graze\DataFlow\Node\File\FileNodeInterface;

interface CompressorInterface
{
    /**
     * Compress a file and return the new file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     * @return FileNodeInterface
     */
    public function compress(FileNodeInterface $node, array $options = []);

    /**
     * Decompress a file and return the decompressed file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     * @return FileNodeInterface
     */
    public function decompress(FileNodeInterface $node, array $options = []);
}
