<?php

namespace Graze\DataFlow\Test\Unit\Definition;

use Graze\DataFlow\Definition\CsvDefinition;
use Graze\DataFlow\Test\TestCase;

class CsvDefinitionTest extends TestCase
{
    public function testImplementsInterface()
    {
        $definition = new CsvDefinition();

        static::assertInstanceOf('Graze\DataFlow\Definition\CsvDefinitionInterface', $definition);
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $definition = new CsvDefinition();

        static::assertEquals(',', $definition->getDelimiter(), "Default Delimiter should be ','");
        static::assertTrue($definition->useQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $definition->getNullOutput(), "Null character should be '\\N'");
        static::assertTrue($definition->getIncludeHeaders(), "Headers should be on by default");
        static::assertEquals("\n", $definition->getLineTerminator(), "Line terminator should be '\\n'");
        static::assertTrue($definition->isUnicode(), "Should be using unicode by default");
        static::assertEquals('"', $definition->getQuoteCharacter(), "Default quote character should be \"");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $definition = new CsvDefinition([
            'delimiter'      => "\t",
            'useQuotes'      => false,
            'nullOutput'     => '',
            'includeHeaders' => false,
            'lineTerminator' => "----",
            "isUnicode"      => false,
        ]);

        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertFalse($definition->useQuotes(), "Quoting should be off");
        static::assertEquals('', $definition->getNullOutput(), "Null character should be '' (blank)'");
        static::assertFalse($definition->getIncludeHeaders(), "Headers should be off");
        static::assertEquals("----", $definition->getLineTerminator(), "Line terminator should be '----'");
        static::assertFalse($definition->isUnicode(), "Unicode should be off");
        static::assertEquals('', $definition->getQuoteCharacter(),
            "Default quote character should be blank when useQuotes is false");
    }
}
