<?php

namespace Graze\DataFlow\Node;

use Graze\DataFlow\Flowable\Extension\FlowExtension;
use Graze\DataFlow\Flowable\Flowable;

class DataNode extends Flowable implements DataNodeInterface
{
    use FlowExtension;
}
