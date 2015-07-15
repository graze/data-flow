<?php

namespace Graze\DataFlow\Definition;

class CsvDefinition implements CsvDefinitionInterface
{
    const DEFAULT_DELIMITER       = ',';
    const DEFAULT_NULL_OUTPUT     = '\\N';
    const DEFAULT_INCLUDE_HEADERS = true;
    const DEFAULT_LINE_TERMINATOR = "\n";
    const DEFAULT_IS_UNICODE      = true;
    const DEFAULT_QUOTE_CHARACTER = '"';

    const OPTION_DELIMITER       = 'delimiter';
    const OPTION_NULL_OUTPUT     = 'nullOutput';
    const OPTION_INCLUDE_HEADERS = 'includeHeaders';
    const OPTION_LINE_TERMINATOR = 'lineTerminator';
    const OPTION_IS_UNICODE      = 'isUnicode';
    const OPTION_QUOTE_CHARACTER = 'quoteCharacter';

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $quoteCharacter;

    /**
     * @var string
     */
    protected $nullOutput;

    /**
     * @var bool
     */
    protected $includeHeaders;

    /**
     * @var string
     */
    protected $lineTerminator;

    /**
     * @param array $options -delimiter <string> (Default: ,) Character to use between fields
     *                       -quoteCharacter <string> (Default: ")
     *                       -nullOutput <string> (Default: \N)
     *                       -includeHeaders <bool> (Default: true)
     *                       -lineTerminator <string> (Default: \n) [Not current implemented]
     */
    public function __construct($options = [])
    {
        $this->options = $options;
        $this->delimiter = $this->getOption(static::OPTION_DELIMITER, static::DEFAULT_DELIMITER);
        $this->quoteCharacter = $this->getOption(static::OPTION_QUOTE_CHARACTER, static::DEFAULT_QUOTE_CHARACTER);
        $this->nullOutput = $this->getOption(static::OPTION_NULL_OUTPUT, static::DEFAULT_NULL_OUTPUT);
        $this->includeHeaders = $this->getOption(static::OPTION_INCLUDE_HEADERS, static::DEFAULT_INCLUDE_HEADERS);
        $this->lineTerminator = $this->getOption(static::OPTION_LINE_TERMINATOR, static::DEFAULT_LINE_TERMINATOR);
    }

    /**
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    private function getOption($name, $default)
    {
        return (isset($this->options[$name])) ? $this->options[$name] : $default;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return bool
     */
    public function useQuotes()
    {
        return $this->quoteCharacter <> '';
    }

    /**
     * @return string
     */
    public function getNullOutput()
    {
        return $this->nullOutput;
    }

    /**
     * @return bool
     */
    public function getIncludeHeaders()
    {
        return $this->includeHeaders;
    }

    /**
     * @return string
     */
    public function getLineTerminator()
    {
        return $this->lineTerminator;
    }

    /**
     * @return string
     */
    public function getQuoteCharacter()
    {
        return $this->quoteCharacter;
    }
}
