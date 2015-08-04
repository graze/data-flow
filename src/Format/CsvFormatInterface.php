<?php

namespace Graze\DataFlow\Format;

interface CsvFormatInterface
{
    /**
     * @return string
     */
    public function getDelimiter();

    /**
     * @param string $delimiter
     * @return CsvFormatInterface
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
     * @return CsvFormatInterface
     */
    public function setQuoteCharacter($quoteCharacter);

    /**
     * @return string
     */
    public function getNullOutput();

    /**
     * @param string $nullOutput
     * @return CsvFormatInterface
     */
    public function setNullOutput($nullOutput);

    /**
     * @return bool
     */
    public function getIncludeHeaders();

    /**
     * @param boolean $includeHeaders
     * @return CsvFormatInterface
     */
    public function setIncludeHeaders($includeHeaders);

    /**
     * @return string
     */
    public function getLineTerminator();

    /**
     * @param string $lineTerminator
     * @return CsvFormatInterface
     */
    public function setLineTerminator($lineTerminator);
}
