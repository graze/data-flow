<?php

namespace Graze\DataFlow\Format;

trait FormatAwareTrait
{
    /**
     * @var FormatInterface|null
     */
    protected $format = null;

    /**
     * @return FormatInterface|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param FormatInterface $format
     * @return $this
     */
    public function setFormat(FormatInterface $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFormatType()
    {
        if ($this->format) {
            return $this->format->getType();
        }
        return null;
    }
}
