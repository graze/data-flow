<?php

namespace Graze\DataFlow\Node\File;

use Graze\DataFlow\Definition\CsvDefinitionInterface;
use Graze\DataFlow\Definition\CsvDefinitionPassThrough;
use Graze\DataFlow\Flow\File\Modify\Compression\CompressionType;

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

    public function __clone()
    {
        $this->csvDefinition = clone $this->csvDefinition;
    }
}
