<?php

namespace Graze\DataFlow\Definition;

trait CsvDefinitionPassThrough
{
    /**
     * @var CsvDefinitionInterface
     */
    protected $csvDefinition;

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->csvDefinition->getDelimiter();
    }

    /**
     * @return bool
     */
    public function useQuotes()
    {
        return $this->csvDefinition->useQuotes();
    }

    /**
     * @return string
     */
    public function getNullOutput()
    {
        return $this->csvDefinition->getNullOutput();
    }

    /**
     * @return bool
     */
    public function getIncludeHeaders()
    {
        return $this->csvDefinition->getIncludeHeaders();
    }

    /**
     * @return string
     */
    public function getLineTerminator()
    {
        return $this->csvDefinition->getLineTerminator();
    }

    /**
     * @return string
     */
    public function getQuoteCharacter()
    {
        return $this->csvDefinition->getQuoteCharacter();
    }
}
