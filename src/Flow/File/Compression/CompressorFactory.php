<?php

namespace Graze\DataFlow\Flow\File\Compression;

use Graze\DataFlow\Flow\FlowInterface;
use Graze\DataFlow\Flowable\FlowableInterface;
use Graze\DataFlow\Node\File\FileNodeInterface;

class CompressorFactory implements FlowInterface
{
    /**
     * Determine if this object can act upon the supplied file
     *
     * @param FlowableInterface $node
     * @param                   $method
     * @return bool
     */
    public function canFlow(FlowableInterface $node, $method)
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
