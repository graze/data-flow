<?php

namespace Graze\DataFlow\Finder;

use League\Container\ServiceProvider;

class DefaultFlowsServiceProvider extends ServiceProvider
{
    /**
     * List of commands to register
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Register all the flows that provide the commands above
     *
     * @return void
     */
    public function register()
    {
        //$this->getContainer()->add('export', 'Path\To\Flow\Export\Class');
        $this->getContainer()->add('test', 'Replace\Me\When\Something\Real\Is\Used');
    }
}
