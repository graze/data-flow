<?php

namespace Graze\DataFlow\Test\Unit;

use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFlow\Flow;
use Graze\DataFlow\Test\TestCase;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeInterface;
use Mockery as m;

class FlowTest extends TestCase
{
    public function testFluentChain()
    {
        $node1 = m::mock(FileNodeInterface::class);
        $node2 = m::mock(NodeInterface::class);

        $collection = new NodeCollection([$node1, $node2]);

        $result = Flow::create()
                    ->filter(function (NodeInterface $node) {
                        return ($node instanceof FileNodeInterface);
                    })
                    ->first()
                    ->flow($collection);

        static::assertSame($result, $node1);
    }
}
