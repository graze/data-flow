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
     * @param string $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        $this->csvDefinition->setDelimiter($delimiter);
        return $this;
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
     * @param string $nullOutput
     * @return $this
     */
    public function setNullOutput($nullOutput)
    {
        $this->csvDefinition->setNullOutput($nullOutput);
        return $this;
    }

    /**
     * @return bool
     */
    public function getIncludeHeaders()
    {
        return $this->csvDefinition->getIncludeHeaders();
    }

    /**
     * @param boolean $includeHeaders
     * @return $this
     */
    public function setIncludeHeaders($includeHeaders)
    {
        $this->csvDefinition->setIncludeHeaders($includeHeaders);
        return $this;
    }

    /**
     * @return string
     */
    public function getLineTerminator()
    {
        return $this->csvDefinition->getLineTerminator();
    }

    /**
     * @param string $lineTerminator
     * @return $this
     */
    public function setLineTerminator($lineTerminator)
    {
        $this->csvDefinition->setLineTerminator($lineTerminator);
        return $this;
    }

    /**
     * @return string
     */
    public function getQuoteCharacter()
    {
        return $this->csvDefinition->getQuoteCharacter();
    }

    /**
     * @param string $quoteCharacter
     * @return $this
     */
    public function setQuoteCharacter($quoteCharacter)
    {
        $this->csvDefinition->setQuoteCharacter($quoteCharacter);
        return $this;
    }
}
