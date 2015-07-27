<?php

namespace Graze\DataFlow\Test\Unit\Node;

use Graze\DataFlow\Test\TestCase;
use Mockery as m;

class DataNodeTest extends TestCase
{
    public function testIsExtensible()
    {
        $node = m::mock('Graze\DataFlow\Node\DataNode');
        static::assertInstanceOf('Graze\Extensible\ExtensibleInterface', $node);
    }

    public function testImplementsDataNodeInterface()
    {
        $node = m::mock('Graze\DataFlow\Node\DataNode');
        static::assertInstanceOf('Graze\DataFlow\Node\DataNodeInterface', $node);
    }
}
