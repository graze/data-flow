<?php

namespace Graze\DataFlow\Test\Unit\Node\File;

use Graze\DataFlow\Format\CsvFormat;
use Graze\DataFlow\Node\File\LocalCsvFile;
use Graze\DataFlow\Node\File\LocalFile;
use Graze\DataFlow\Test\TestCase;

class LocalCsvFileTest extends TestCase
{
    public function testCloneWillCloneTheCsvDefinition()
    {
        $file = (new LocalFile('some/path/here'))
            ->setFormat(new CsvFormat());
        $clone = $file->getClone();

        static::assertNotSame($file, $clone);

        $clone->getFormat()->setDelimiter('--');

        static::assertNotEquals($file->getFormat()->getDelimiter(), $clone->getFormat()->getDelimiter());
    }

    public function testImplementsInterface()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat());

        static::assertInstanceOf('Graze\DataFlow\Format\FormatAwareInterface', $file);
        static::assertInstanceOf('Graze\DataFlow\Format\FormatInterface', $file->getFormat());
        static::assertInstanceOf('Graze\DataFlow\Format\CsvFormatInterface', $file->getFormat());
    }

    public function testFormatTypeIsCsv()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat());

        static::assertEquals('csv', $file->getFormatType());
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat());

        $format = $file->getFormat();

        static::assertInstanceOf('Graze\DataFlow\Format\CsvFormatInterface', $format);

        static::assertEquals(',', $format->getDelimiter(), "Default Delimiter should be ','");
        static::assertTrue($format->useQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $format->getNullOutput(), "Null character should be '\\N'");
        static::assertTrue($format->getIncludeHeaders(), "Headers should be on by default");
        static::assertEquals("\n", $format->getLineTerminator(), "Line terminator should be '\\n'");
        static::assertEquals('"', $format->getQuoteCharacter(), "Default quote character should be \"");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat([
                'delimiter'      => "\t",
                'quoteCharacter' => '',
                'nullOutput'     => '',
                'includeHeaders' => false,
                'lineTerminator' => "----",
            ]));
        
        $format = $file->getFormat();

        static::assertEquals("\t", $format->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertFalse($format->useQuotes(), "Quoting should be off");
        static::assertEquals('', $format->getNullOutput(), "Null character should be '' (blank)'");
        static::assertFalse($format->getIncludeHeaders(), "Headers should be off");
        static::assertEquals("----", $format->getLineTerminator(), "Line terminator should be '----'");
        static::assertEquals('', $format->getQuoteCharacter(),
            "Default quote character should be blank when useQuotes is false");
    }

    public function testSettingOptionsModifiesTheDefinition()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat());
        $format = $file->getFormat();

        static::assertSame($format, $format->setDelimiter("\t"), "SetDelimiter should be fluent");
        static::assertEquals("\t", $format->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertSame($format, $format->setQuoteCharacter(''), "setQuoteCharacter should be fluent");
        static::assertEquals('', $format->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($format->useQuotes(), "Quoting should be off");
        static::assertSame($format, $format->setNullOutput(''), "setNullOutput should be fluent");
        static::assertEquals('', $format->getNullOutput(), "Null character should be '' (blank)'");
        static::assertSame($format, $format->setIncludeHeaders(false), "setIncludeHeaders should be fluent");
        static::assertFalse($format->getIncludeHeaders(), "Headers should be off");
        static::assertSame($format, $format->setLineTerminator('----'), "setLineTerminator should be fluent");
        static::assertEquals("----", $format->getLineTerminator(), "Line terminator should be '----'");
    }
}
