<?php

namespace Graze\DataFlow\Flow\File\Modify\Compression;

use Graze\DataFlow\Flow\Flow;
use Graze\DataFlow\Node\File\FileNodeInterface;
use Graze\Extensible\ExtensibleInterface;
use Graze\Extensible\ExtensionInterface;

class CompressorFactory extends Flow implements ExtensionInterface
{
    /**
     * Determine if this object can act upon the supplied file
     *
     * @param ExtensibleInterface $node
     * @param string              $method
     * @return bool
     */
    public function canExtend(ExtensibleInterface $node, $method)
    {
        return ($node instanceof FileNodeInterface);
    }

    /**
     * @param string $compression CompressionType::
     * @return CompressorInterface
     * @throws InvalidCompressionTypeException
     */
    protected function getCompressor($compression)
    {
        switch ($compression) {
            case CompressionType::GZIP:
                return new Gzip();
            case CompressionType::ZIP :
                return new Zip();
            case CompressionType::NONE:
            default:
                throw new InvalidCompressionTypeException($compression);
        }
    }

    /**
     * @extend Graze\DataFlow\Node\File\FileNodeInterface
     *
     * @param FileNodeInterface $file
     * @param string            $compression CompressionType::
     * @param array             $options     Options to be passed to the compressor
     * @return FileNodeInterface
     */
    public function compress(FileNodeInterface $file, $compression, array $options = [])
    {
        $compressor = $this->getCompressor($compression);

        return $compressor->compress($file, $options);
    }

    /**
     * @extend Graze\DataFlow\Node\File\FileNodeInterface
     *
     * @param FileNodeInterface $file
     * @param array             $options Options to be passed to the compressor
     * @return FileNodeInterface
     */
    public function decompress(FileNodeInterface $file, array $options = [])
    {
        $compressor = $this->getCompressor($file->getCompression());

        return $compressor->decompress($file, $options);
    }
}
