<?php

namespace Graze\DataFlow\Test\Unit\Format;

use Graze\DataFlow\Format\FormatAwareInterface;
use Graze\DataFlow\Test\Format\FakeFormatAware;
use Graze\DataFlow\Test\TestCase;
use Mockery as m;

class FormatAwareTraitTest extends TestCase
{
    /**
     * @var FakeFormatAware
     */
    protected $formatAware;

    public function setUp()
    {
        $this->formatAware = new FakeFormatAware();
    }

    public function testSetFormat()
    {
        $format = m::mock('Graze\DataFlow\Format\FormatInterface');
        $this->formatAware->setFormat($format);

        static::assertSame($format, $this->formatAware->getFormat());
    }

    public function testGetFormatType()
    {
        $format = m::mock('Graze\DataFlow\Format\FormatInterface');
        $this->formatAware->setFormat($format);

        $format->shouldReceive('getType')
            ->andReturn('test_format');

        static::assertEquals('test_format', $this->formatAware->getFormatType());
    }

    public function testGetFormatTypeWillReturnNullWithNoFormatIsSpecified()
    {
        static::assertNull($this->formatAware->getFormatType());
    }

    public function testCloningWillCloneFormat()
    {
        $format = m::mock('Graze\DataFlow\Format\FormatInterface');
        $this->formatAware->setFormat($format);

        $newFormatAware = clone $this->formatAware;

        static::assertNotSame($this->formatAware, $newFormatAware);
        static::assertNotSame($this->formatAware->getFormat(), $newFormatAware->getFormat());
    }
}
