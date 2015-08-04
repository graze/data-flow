<?php

namespace Graze\DataFlow\Test\Unit\Format;

use Graze\DataFlow\Format\CsvFormat;
use Graze\DataFlow\Test\TestCase;

class CsvFormatTest extends TestCase
{
    public function testImplementsInterface()
    {
        $definition = new CsvFormat();

        static::assertInstanceOf('Graze\DataFlow\Format\CsvFormatInterface', $definition);
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $definition = new CsvFormat();

        static::assertEquals(',', $definition->getDelimiter(), "Default Delimiter should be ','");
        static::assertEquals('"', $definition->getQuoteCharacter(), "Default quote character should be \"");
        static::assertTrue($definition->useQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $definition->getNullOutput(), "Null character should be '\\N'");
        static::assertTrue($definition->getIncludeHeaders(), "Headers should be on by default");
        static::assertEquals("\n", $definition->getLineTerminator(), "Line terminator should be '\\n'");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $definition = new CsvFormat([
            'delimiter'      => "\t",
            'quoteCharacter' => '',
            'nullOutput'     => '',
            'includeHeaders' => false,
            'lineTerminator' => "----"
        ]);

        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertEquals('', $definition->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($definition->useQuotes(), "Quoting should be off");
        static::assertEquals('', $definition->getNullOutput(), "Null character should be '' (blank)'");
        static::assertFalse($definition->getIncludeHeaders(), "Headers should be off");
        static::assertEquals("----", $definition->getLineTerminator(), "Line terminator should be '----'");
    }

    public function testSettingProperties()
    {
        $definition = new CsvFormat();

        static::assertEquals(',', $definition->getDelimiter(), "Default Delimiter should be ','");
        static::assertEquals('"', $definition->getQuoteCharacter(), "Default quote character should be \"");
        static::assertTrue($definition->useQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $definition->getNullOutput(), "Null character should be '\\N'");
        static::assertTrue($definition->getIncludeHeaders(), "Headers should be on by default");
        static::assertEquals("\n", $definition->getLineTerminator(), "Line terminator should be '\\n'");

        static::assertSame($definition, $definition->setDelimiter("\t"), "SetDelimiter should be fluent");
        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertSame($definition, $definition->setQuoteCharacter(''), "setQuoteCharacter should be fluent");
        static::assertEquals('', $definition->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($definition->useQuotes(), "Quoting should be off");
        static::assertSame($definition, $definition->setNullOutput(''), "setNullOutput should be fluent");
        static::assertEquals('', $definition->getNullOutput(), "Null character should be '' (blank)'");
        static::assertSame($definition, $definition->setIncludeHeaders(false), "setIncludeHeaders should be fluent");
        static::assertFalse($definition->getIncludeHeaders(), "Headers should be off");
        static::assertSame($definition, $definition->setLineTerminator('----'), "setLineTerminator should be fluent");
        static::assertEquals("----", $definition->getLineTerminator(), "Line terminator should be '----'");
    }
}
