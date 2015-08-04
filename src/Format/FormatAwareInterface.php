<?php

namespace Graze\DataFlow\Format;

interface FormatAwareInterface
{
    /**
     * Get the format for this object
     *
     * @return FormatInterface|null
     */
    public function getFormat();

    /**
     * Set the format for this object
     *
     * @param FormatInterface $format
     * @return $this
     */
    public function setFormat(FormatInterface $format);

    /**
     * Get the type of format for this object, if there is no format specified null is returned
     *
     * @return string|null
     */
    public function getFormatType();
}
