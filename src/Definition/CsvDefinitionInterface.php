<?php

namespace Graze\DataFlow\Definition;

interface CsvDefinitionInterface
{
    /**
     * @return string
     */
    public function getDelimiter();

    /**
     * @return bool
     */
    public function useQuotes();

    /**
     * @return string
     */
    public function getQuoteCharacter();

    /**
     * @return string
     */
    public function getNullOutput();

    /**
     * @return bool
     */
    public function getIncludeHeaders();

    /**
     * @return string
     */
    public function getLineTerminator();
}
