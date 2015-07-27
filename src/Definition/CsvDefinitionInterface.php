<?php

namespace Graze\DataFlow\Definition;

interface CsvDefinitionInterface
{
    /**
     * @return string
     */
    public function getDelimiter();

    /**
     * @param string $delimiter
     * @return CsvDefinitionInterface
     */
    public function setDelimiter($delimiter);

    /**
     * @return bool
     */
    public function useQuotes();

    /**
     * @return string
     */
    public function getQuoteCharacter();

    /**
     * @param string $quoteCharacter
     * @return CsvDefinitionInterface
     */
    public function setQuoteCharacter($quoteCharacter);

    /**
     * @return string
     */
    public function getNullOutput();

    /**
     * @param string $nullOutput
     * @return CsvDefinitionInterface
     */
    public function setNullOutput($nullOutput);

    /**
     * @return bool
     */
    public function getIncludeHeaders();

    /**
     * @param boolean $includeHeaders
     * @return CsvDefinitionInterface
     */
    public function setIncludeHeaders($includeHeaders);

    /**
     * @return string
     */
    public function getLineTerminator();

    /**
     * @param string $lineTerminator
     * @return CsvDefinitionInterface
     */
    public function setLineTerminator($lineTerminator);
}
