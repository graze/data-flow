<?php

namespace Graze\DataFlow\Test\Unit\Node\File;

use Graze\DataFlow\Definition\CsvDefinition;
use Graze\DataFlow\Node\File\LocalCsvFile;
use Graze\DataFlow\Test\TestCase;

class LocalCsvFileTest extends TestCase
{
    public function testCloneWillCloneTheCsvDefinition()
    {
        $file = new LocalCsvFile('some/path/here', new CsvDefinition());
        $clone = $file->getClone();

        static::assertNotSame($file, $clone);

        $clone->setDelimiter('--');

        static::assertNotEquals($file->getDelimiter(), $clone->getDelimiter());
    }

    public function testImplementsInterface()
    {
        $file = new LocalCsvFile(
            'fake/path',
            new CsvDefinition()
        );

        static::assertInstanceOf('Graze\DataFlow\Definition\CsvDefinitionInterface', $file);
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $file = new LocalCsvFile(
            'fake/path',
            new CsvDefinition()
        );

        static::assertEquals(',', $file->getDelimiter(), "Default Delimiter should be ','");
        static::assertTrue($file->useQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $file->getNullOutput(), "Null character should be '\\N'");
        static::assertTrue($file->getIncludeHeaders(), "Headers should be on by default");
        static::assertEquals("\n", $file->getLineTerminator(), "Line terminator should be '\\n'");
        static::assertEquals('"', $file->getQuoteCharacter(), "Default quote character should be \"");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $file = new LocalCsvFile(
            'fake/path',
            new CsvDefinition([
                'delimiter'      => "\t",
                'quoteCharacter' => '',
                'nullOutput'     => '',
                'includeHeaders' => false,
                'lineTerminator' => "----",
            ])
        );

        static::assertEquals("\t", $file->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertFalse($file->useQuotes(), "Quoting should be off");
        static::assertEquals('', $file->getNullOutput(), "Null character should be '' (blank)'");
        static::assertFalse($file->getIncludeHeaders(), "Headers should be off");
        static::assertEquals("----", $file->getLineTerminator(), "Line terminator should be '----'");
        static::assertEquals('', $file->getQuoteCharacter(),
            "Default quote character should be blank when useQuotes is false");
    }

    public function testSettingOptionsModifiesTheDefinition()
    {
        $file = new LocalCsvFile(
            'fake/path',
            new CsvDefinition()
        );

        static::assertSame($file, $file->setDelimiter("\t"), "SetDelimiter should be fluent");
        static::assertEquals("\t", $file->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertSame($file, $file->setQuoteCharacter(''), "setQuoteCharacter should be fluent");
        static::assertEquals('', $file->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($file->useQuotes(), "Quoting should be off");
        static::assertSame($file, $file->setNullOutput(''), "setNullOutput should be fluent");
        static::assertEquals('', $file->getNullOutput(), "Null character should be '' (blank)'");
        static::assertSame($file, $file->setIncludeHeaders(false), "setIncludeHeaders should be fluent");
        static::assertFalse($file->getIncludeHeaders(), "Headers should be off");
        static::assertSame($file, $file->setLineTerminator('----'), "setLineTerminator should be fluent");
        static::assertEquals("----", $file->getLineTerminator(), "Line terminator should be '----'");
    }
}
