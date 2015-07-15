<?php

namespace Graze\DataFlow\Test\Unit\Node;

use Graze\DataFlow\Node\DataNode;
use Graze\DataFlow\Test\TestCase;

class DataNodeTest extends TestCase
{
    public function testIsExtensible()
    {
        $node = new DataNode();
        static::assertInstanceOf('Graze\Extensible\ExtensibleInterface', $node);
    }

    public function testImplementsDataNodeInterface()
    {
        $node = new DataNode();
        static::assertInstanceOf('Graze\DataFlow\Node\DataNodeInterface', $node);
    }
}
