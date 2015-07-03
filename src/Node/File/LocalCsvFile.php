<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataFlow\Flow\File\Compression\CompressionType;
use Graze\DataFlow\Definition\CsvDefinitionInterface;
use Graze\DataFlow\Definition\CsvDefinitionPassThrough;

class LocalCsvFile extends LocalFile implements CsvFileNodeInterface
{
    use CsvDefinitionPassThrough;

    /**
     * @param string                 $filePath
     * @param CsvDefinitionInterface $csvDefinition
     * @param string                 $compression
     */
    public function __construct($filePath, CsvDefinitionInterface $csvDefinition, $compression = CompressionType::NONE)
    {
        parent::__construct($filePath, $compression);
        $this->csvDefinition = $csvDefinition;
    }
}
