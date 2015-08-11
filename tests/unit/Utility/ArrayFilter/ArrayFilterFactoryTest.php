<?php

namespace Graze\DataFlow\Test\Unit\Node\File\MetadataFilter;

use Graze\DataFlow\Test\TestCase;
use Graze\DataFlow\Utility\ArrayFilter\ArrayFilterFactory;

class ArrayFilterFactoryTest extends TestCase
{
    /**
     * @var ArrayFilterFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new ArrayFilterFactory();
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf('Graze\DataFlow\Utility\ArrayFilter\ArrayFilterFactoryInterface',
            $this->factory);
    }

    /**
     * @dataProvider createFilterData
     * @param string $property
     * @param mixed  $expected
     * @param array  $metadata
     * @param bool   $result
     */
    public function testCreateFilter($property, $expected, $metadata, $result)
    {
        $filter = $this->factory->createFilter($property, $expected);
        static::assertEquals($result, $filter->matches($metadata),
            sprintf("Expected %s and %s to %s for property: %s",
                json_encode($expected),
                json_encode($metadata),
                $result,
                $property
            )
        );
    }

    /**
     * @return array
     */
    public function createFilterData()
    {
        return [
            ['test', 'value', ['test' => 'value'], true],
            ['test', 'value', ['test' => 'value2'], false],
            ['test =', 'value', ['test' => 'value'], true],
            ['test =', 'value', ['test' => 'value2'], false],
            ['test >', 10, ['test' => 12], true],
            ['test >', 12, ['test' => 10], false],
            ['test >', 10, ['test' => 10], false],
            ['test >=', 10, ['test' => 12], true],
            ['test >=', 12, ['test' => 10], false],
            ['test >=', 10, ['test' => 10], true],
            ['test <', 10, ['test' => 12], false],
            ['test <', 12, ['test' => 10], true],
            ['test <', 10, ['test' => 10], false],
            ['test <=', 10, ['test' => 12], false],
            ['test <=', 12, ['test' => 10], true],
            ['test <=', 10, ['test' => 10], true],
            ['test !=', 'value', ['test' => 'other'], true],
            ['test !=', 'value', ['test' => 'value'], false],
            ['test <>', 'value', ['test' => 'other'], true],
            ['test <>', 'value', ['test' => 'value'], false],
            ['test ~', '/word\d+stuff/i', ['test' => 'word12346234stuFF'], true],
            ['test ~', '/word\d+stuff/', ['test' => 'word12346234stuFF'], false],
            ['test ~=', '/word\d+stuff/i', ['test' => 'word12346234stuFF'], true],
            ['test ~=', '/word\d+stuff/', ['test' => 'word12346234stuFF'], false],
            ['test in', ['1', '2'], ['test' => '1'], true],
            ['test in', ['1', '2'], ['test' => '2'], true],
            ['test in', ['1', '2'], ['test' => '3'], false],
        ];
    }

    /**
     * @dataProvider invalidPropertyNames
     * @param string $property
     */
    public function testCreateFilterWillThrowExceptionWithInvalidProperty($property)
    {
        static::setExpectedException(
            'Graze\DataFlow\Utility\ArrayFilter\Exception\UnknownPropertyDefinitionException',
            "Unknown property definition: $property"
        );

        $this->factory->createFilter($property, '');
    }

    /**
     * @return array
     */
    public function invalidPropertyNames()
    {
        return [
            ['test = '],
            ['test jdksla'],
            ['  something'],
            ['test !!'],
            ['stuff space ='],
            ['things <<'],
            ['stuff >>'],
            ['']
        ];
    }

    /**
     * @dataProvider createFiltersTestData
     * @param array $configuration
     * @param array $metadata
     * @param bool  $result
     */
    public function testCreateFilters(array $configuration, array $metadata, $result)
    {
        $filter = $this->factory->createFilters($configuration);
        static::assertEquals($result, $filter->matches($metadata),
            sprintf("Expected configuration: %s and data: %s to result in: %s",
                json_encode($configuration),
                json_encode($metadata),
                $result
            )
        );
    }

    /**
     * @return array
     */
    public function createFiltersTestData()
    {
        return [
            [['test' => 'value'], ['test' => 'value'], true],
            [['test' => 'value', 'test2' => 'value'], ['test' => 'value', 'test2' => 'value'], true],
            [['test' => 'value', 'test2' => 'value'], ['test' => 'value', 'test2' => 'value2'], false],
            [['test >' => 4, 'test <' => 8], ['test' => 6], true],
            [['test >' => 4, 'test <' => 8], ['test' => 2], false],
        ];
    }
}
