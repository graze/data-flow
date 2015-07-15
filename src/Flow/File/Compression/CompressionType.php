<?php

namespace Graze\DataFlow\Flow\File\Compression;

class CompressionType
{
    const GZIP    = 'gzip';
    const ZIP     = 'zip';
    const NONE    = 'none';
    const UNKNOWN = 'unknown';

    /**
     * @return array
     */
    public static function getCompressionTypes()
    {
        return [
            static::GZIP,
            static::ZIP
        ];
    }
}
